<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Services\TapPaymentService;

class TapWebhookController extends Controller
{
    protected TapPaymentService $tapService;
    protected InvoiceService $invoiceService;

    public function __construct(TapPaymentService $tapService, InvoiceService $invoiceService)
    {
        $this->tapService = $tapService;
        $this->invoiceService = $invoiceService;
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $data = $request->all();
        $event = $request->input('event');

        // Log the incoming webhook
        \App\Models\PaymentLog::create([
            'type' => 'webhook_received',
            'endpoint' => $request->fullUrl(),
            'method' => 'POST',
            'payload' => $data,
            'ip_address' => $request->ip(),
        ]);

        // 1. Verify Signature (Optional but recommended for production)
        $signatureHeader = $request->header('Tap-Signature');
        if (!$this->tapService->verifyWebhookSignature($payload, $signatureHeader ?? '')) {
            Log::warning('Tap Webhook Signature Verification Failed', ['ip' => $request->ip()]);
            // return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = $request->input('data');
        if (!$event || !$data) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $transactionId = $data['reference']['transaction'] ?? null;
        $tapChargeId = $data['id'] ?? null;

        if (!$transactionId) {
            return response()->json(['error' => 'Missing transaction reference'], 400);
        }

        return DB::transaction(function () use ($transactionId, $tapChargeId, $event, $data) {
            $transaction = Transaction::where('id', $transactionId)->lockForUpdate()->first();

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Idempotency check: If already finalized, just return success
            if (in_array($transaction->status, ['captured', 'authorized'])) {
                return response()->json(['status' => 'already_processed']);
            }

            $tapCustomerId = $data['customer']['id'] ?? null;
            $cardToken = $data['card']['id'] ?? null;

            switch ($event) {
                case 'charge.succeeded':
                case 'authorize.succeeded':
                    $status = ($event === 'charge.succeeded') ? 'captured' : 'authorized';
                    
                    $transaction->update([
                        'status' => $status,
                        'tap_charge_id' => $tapChargeId
                    ]);

                    $user = User::find($transaction->user_id);
                    if ($user && ($tapCustomerId || $cardToken)) {
                        $user->update(['tap_customer_id' => $tapCustomerId, 'card_token' => $cardToken]);
                    }

                    $nextBillingDate = ($transaction->billing_cycle === 'annually') ? now()->addYear() : now()->addMonth();
                    $billingPeriod = now()->format('M d, Y') . ' - ' . $nextBillingDate->format('M d, Y');

                    // Handle Plan Upgrade
                    if ($transaction->plan_id && $transaction->type === 'subscription' && $status === 'captured') {
                        $user->update(['plan_id' => $transaction->plan_id]);
                        $user->enforcePlanLimits();

                        Subscription::updateOrCreate(
                            ['client_id' => $user->id, 'plan_id' => $transaction->plan_id, 'service_id' => null],
                            [
                                'billing_cycle' => $transaction->billing_cycle,
                                'status' => 'active',
                                'starts_at' => now(),
                                'ends_at' => $nextBillingDate,
                                'next_billing_date' => $nextBillingDate,
                            ]
                        );

                        Payment::create([
                            'user_id' => $user->id,
                            'amount' => $transaction->amount,
                            'payment_method' => 'tap',
                            'transaction_id' => $tapChargeId,
                            'status' => 'released',
                            'plan_id' => $transaction->plan_id,
                        ]);
                    }

                    // Handle Project Payment
                    if ($transaction->project_id) {
                        $project = Project::find($transaction->project_id);
                        $project->update(['status' => 'active']);

                        if ($transaction->type === 'subscription') {
                            Subscription::updateOrCreate(
                                [
                                    'client_id' => $user->id, 
                                    'service_id' => $project->service_id,
                                    'company_id' => $project->company_id,
                                ],
                                [
                                    'provider_id' => $project->provider_id,
                                    'billing_cycle' => $transaction->billing_cycle,
                                    'status' => 'active',
                                    'starts_at' => now(),
                                    'ends_at' => $nextBillingDate,
                                    'next_billing_date' => $nextBillingDate,
                                ]
                            );
                        }

                        Payment::create([
                            'project_id' => $project->id,
                            'user_id' => $user->id,
                            'amount' => $transaction->amount,
                            'payment_method' => 'tap',
                            'transaction_id' => $tapChargeId,
                            'status' => $status === 'captured' ? 'released' : 'held_in_escrow',
                        ]);

                        ProjectHistory::create([
                            'project_id' => $project->id,
                            'user_id' => $user->id,
                            'action' => 'payment_confirmed',
                            'description' => 'Payment confirmed via webhook.',
                        ]);
                    }

                    $invoice = $this->invoiceService->generateForTransaction($transaction);
                    $invoice->update(['billing_period' => $billingPeriod]);
                    break;

                case 'charge.failed':
                case 'charge.declined':
                    $transaction->update(['status' => 'failed']);
                    break;
            }

            return response()->json(['status' => 'success']);
        });
    }
}

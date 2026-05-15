<?php

namespace Modules\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class TapWebhookController extends Controller
{
    protected TapPaymentService $tapService;

    public function __construct(TapPaymentService $tapService)
    {
        $this->tapService = $tapService;
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $data = $request->all();
        $event = $request->input('event');

        // Log the incoming webhook
        \App\Models\PaymentLog::create([
            'type' => 'webhook_received',
            'event' => $event,
            'endpoint' => $request->fullUrl(),
            'method' => 'POST',
            'payload' => $data,
            'ip_address' => $request->ip(),
        ]);

        // 1. Verify Signature
        $signatureHeader = $request->header('Tap-Signature');

        if (!$this->tapService->verifyWebhookSignature($payload, $signatureHeader ?? '')) {
            Log::warning('Tap Webhook Signature Verification Failed', [
                'ip' => $request->ip(),
                'payload' => $request->all(),
            ]);
            // Return 400 or 401 to let Tap know it failed verification
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if (!$event || !$data) {
            return response()->json(['error' => 'Invalid payload structure'], 400);
        }

        // Tap usually passes our reference in 'reference.transaction'
        $transactionId = $data['reference']['transaction'] ?? null;
        $tapChargeId = $data['id'] ?? null;
        $tapCustomerId = $data['customer']['id'] ?? null;
        $cardToken = $data['card']['id'] ?? null; // This is the saved card token if save_card was true

        if (!$transactionId || !$tapChargeId) {
            Log::error('Tap Webhook Missing IDs', ['data' => $data]);
            return response()->json(['error' => 'Missing transaction reference'], 400);
        }

        DB::beginTransaction();

        try {
            $transaction = DB::table('transactions')->where('id', $transactionId)->lockForUpdate()->first();

            if (!$transaction) {
                DB::rollBack();
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Always update tap_charge_id as we might only get it here if it wasn't returned earlier
            DB::table('transactions')->where('id', $transactionId)->update([
                'tap_charge_id' => $tapChargeId,
                'updated_at' => now(),
            ]);

            // 2. Handle Events
            switch ($event) {
                case 'charge.succeeded':
                case 'authorize.succeeded':
                    $status = ($event === 'charge.succeeded') ? 'captured' : 'authorized';
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => $status]);
                    
                    $nextBillingDate = ($transaction->billing_cycle === 'annually') ? now()->addYear() : now()->addMonth();
                    $billingPeriod = now()->format('M Y') . ' - ' . $nextBillingDate->format('M Y');

                    // Save card details if available
                    if ($tapCustomerId || $cardToken) {
                        $user = \App\Models\User::find($transaction->user_id);
                        if ($user) {
                            $user->update([
                                'tap_customer_id' => $tapCustomerId ?? $user->tap_customer_id,
                                'card_token' => $cardToken ?? $user->card_token,
                            ]);
                        }
                    }

                    if ($transaction->project_id) {
                        // Set project to active as per latest requirement
                        DB::table('projects')->where('id', $transaction->project_id)->update([
                            'status' => 'active',
                            'updated_at' => now(),
                        ]);

                        // Record in payments table for application logic
                        Payment::create([
                            'project_id' => $transaction->project_id,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->amount,
                            'payment_method' => 'tap',
                            'transaction_id' => $tapChargeId,
                            'status' => $status === 'captured' ? 'released' : 'held_in_escrow',
                        ]);

                        // Record history
                        DB::table('project_histories')->insert([
                            'project_id' => $transaction->project_id,
                            'user_id' => $transaction->user_id,
                            'action' => 'payment_confirmed',
                            'description' => 'Payment confirmed via Tap. Project is now active.',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Create/Update Subscription for Business Service if it's a subscription type
                        if ($transaction->type === 'subscription') {
                            $project = DB::table('projects')->find($transaction->project_id);
                            \App\Models\Subscription::updateOrCreate(
                                [
                                    'client_id' => $transaction->user_id,
                                    'service_id' => $project->service_id,
                                ],
                                [
                                    'provider_id' => $project->provider_id,
                                    'billing_cycle' => $transaction->billing_cycle,
                                    'status' => 'active',
                                    'starts_at' => now(),
                                    'ends_at' => $nextBillingDate,
                                    'next_billing_date' => $nextBillingDate,
                                    'card_token' => $cardToken,
                                    'tap_customer_id' => $tapCustomerId,
                                ]
                            );
                        }
                    }

                    if ($transaction->plan_id && $event === 'charge.succeeded') {
                        $user = \App\Models\User::find($transaction->user_id);
                        if ($user) {
                            $user->update(['plan_id' => $transaction->plan_id]);
                            $user->enforcePlanLimits();

                            // Create/Update Platform Subscription
                            \App\Models\Subscription::updateOrCreate(
                                [
                                    'client_id' => $user->id,
                                    'plan_id' => $transaction->plan_id,
                                    'service_id' => null, // Platform Plan
                                ],
                                [
                                    'billing_cycle' => $transaction->billing_cycle,
                                    'status' => 'active',
                                    'starts_at' => now(),
                                    'ends_at' => $nextBillingDate,
                                    'next_billing_date' => $nextBillingDate,
                                    'card_token' => $cardToken,
                                    'tap_customer_id' => $tapCustomerId,
                                ]
                            );

                            // Record in payments table
                            Payment::create([
                                'plan_id' => $transaction->plan_id,
                                'user_id' => $transaction->user_id,
                                'amount' => $transaction->amount,
                                'payment_method' => 'tap',
                                'transaction_id' => $tapChargeId,
                                'status' => 'released',
                            ]);
                        }
                    }

                    // Create Invoice with billing period
                    $invoiceNumber = 'INV-' . strtoupper(\Illuminate\Support\Str::random(8));
                    \App\Models\Invoice::create([
                        'transaction_id' => $transactionId,
                        'invoice_number' => $invoiceNumber,
                        'billing_period' => $billingPeriod,
                        'billing_details' => [
                            'amount' => $transaction->amount,
                            'currency' => $transaction->currency,
                            'date' => now()->toDateTimeString(),
                            'cycle' => $transaction->billing_cycle,
                        ],
                    ]);

                    break;

                case 'charge.failed':
                case 'charge.declined':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'failed']);
                    
                    if ($transaction->project_id) {
                        DB::table('projects')->where('id', $transaction->project_id)->update([
                            'status' => 'cancelled',
                            'updated_at' => now(),
                        ]);

                        DB::table('project_histories')->insert([
                            'project_id' => $transaction->project_id,
                            'user_id' => $transaction->user_id,
                            'action' => 'payment_failed',
                            'description' => 'Payment failed via Tap. Project cancelled.',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;

                case 'refund.succeeded':
                    DB::table('transactions')->where('id', $transactionId)->update(['status' => 'refunded']);
                    // event(new PaymentRefunded($transactionId));
                    break;

                default:
                    Log::info("Tap Webhook Unhandled Event: {$event}", ['transaction_id' => $transactionId]);
                    break;
            }

            DB::commit();

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tap Webhook Processing Error', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}

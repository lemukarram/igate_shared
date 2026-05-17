<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Services\InvoiceService;
use App\Settings\PaymentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Services\TapPaymentService;

class CheckoutController extends Controller
{
    protected TapPaymentService $tapService;
    protected InvoiceService $invoiceService;

    public function __construct(TapPaymentService $tapService, InvoiceService $invoiceService)
    {
        $this->tapService = $tapService;
        $this->invoiceService = $invoiceService;
    }

    public function review($providerServiceId)
    {
        $ps = \App\Models\ProviderService::with(['service', 'provider.providerProfile'])->findOrFail($providerServiceId);
        $user = auth()->user();
        $companies = $user->companies;
        
        // Identify companies that already have an active project for this specific provider and service
        $subscribedCompanyIds = Project::where('client_id', $user->id)
            ->where('provider_id', $ps->provider_id)
            ->where('service_id', $ps->service_id)
            ->whereHas('transactions', function($q) {
                $q->whereIn('status', ['authorized', 'captured']);
            })
            ->pluck('company_id')
            ->toArray();

        // If user has only one company and it's already subscribed, redirect to project page
        if ($companies->count() === 1 && in_array($companies->first()->id, $subscribedCompanyIds)) {
            $existingProject = Project::where('client_id', $user->id)
                ->where('company_id', $companies->first()->id)
                ->where('provider_id', $ps->provider_id)
                ->where('service_id', $ps->service_id)
                ->first();
            
            return redirect()->route('projects.show', $existingProject->id)
                ->with('info', 'You already have an active project for this service with this provider.');
        }

        // If ALL companies are subscribed, redirect to the first one's project page
        if ($companies->count() > 0 && count($subscribedCompanyIds) === $companies->count()) {
            $existingProject = Project::where('client_id', $user->id)
                ->where('company_id', $companies->first()->id)
                ->where('provider_id', $ps->provider_id)
                ->where('service_id', $ps->service_id)
                ->first();
                
            return redirect()->route('projects.show', $existingProject->id)
                ->with('info', 'All your companies already have an active project for this service.');
        }

        return view('client.checkout.review', compact('ps', 'companies', 'subscribedCompanyIds'));
    }

    public function process(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'provider_service_id' => 'required|exists:provider_services,id',
            'company_id' => 'required|exists:companies,id',
            'billing_cycle' => 'required|in:monthly,annually',
        ]);

        $ps = \App\Models\ProviderService::findOrFail($request->provider_service_id);
        $billingCycle = $request->billing_cycle;

        $existingProject = Project::where('client_id', $user->id)
            ->where('company_id', $request->company_id)
            ->where('provider_id', $ps->provider_id)
            ->where('service_id', $ps->service_id)
            ->whereHas('transactions', function($q) {
                $q->whereIn('status', ['authorized', 'captured']);
            })
            ->first();

        if ($existingProject) {
            return redirect()->route('projects.show', $existingProject->id)
                ->with('info', 'An active project for this service already exists.');
        }

        if ($user->plan && $user->projects()->where('status', 'active')->count() >= $user->plan->max_projects) {
            return redirect()->route('settings.plan.upgrade')->with('error', 'Plan project limit reached.');
        }

        $amount = ($billingCycle === 'annually') ? $ps->annual_price : $ps->monthly_price;

        $project = Project::create([
            'client_id' => $user->id,
            'company_id' => $request->company_id,
            'provider_id' => $ps->provider_id,
            'service_id' => $ps->service_id,
            'provider_service_id' => $ps->id,
            'status' => 'pending_payment',
            'total_amount' => $amount,
            'start_date' => now(),
        ]);

        ProjectHistory::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'action' => 'project_initiated',
            'description' => 'Project initiated, awaiting payment.',
        ]);

        $transactionId = (string) \Illuminate\Support\Str::ulid();
        $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

        $customer = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => ['country_code' => '966', 'number' => '500000000']
        ];

        try {
            $isEscrow = !str_contains(strtolower($ps->service->name), 'subscription');
            $type = $isEscrow ? 'service_escrow' : 'subscription';

            $tapResponse = $this->tapService->createCharge($amount, $customer, $redirectUrl, $isEscrow, false);
            
            DB::table('transactions')->insert([
                'id' => $transactionId,
                'user_id' => $user->id,
                'provider_id' => $ps->provider_id,
                'project_id' => $project->id,
                'tap_charge_id' => $tapResponse['id'],
                'amount' => $amount,
                'currency' => 'SAR',
                'status' => 'pending',
                'type' => $type,
                'billing_cycle' => $billingCycle,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->away($tapResponse['url']);
        } catch (\Exception $e) {
            $project->delete();
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function planReview($planId, Request $request)
    {
        $plan = \App\Models\Plan::findOrFail($planId);
        $user = auth()->user();
        $billingCycle = $request->query('billing_cycle', 'monthly');
        
        if ($plan->type !== $user->role) {
            return redirect()->route('client.portfolio')->with('error', 'Invalid plan type.');
        }

        return view('client.checkout.plan_review', compact('plan', 'billingCycle'));
    }

    public function processPlan(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,annually',
        ]);

        $plan = \App\Models\Plan::findOrFail($request->plan_id);
        $billingCycle = $validated['billing_cycle'];

        if ($plan->type !== $user->role) {
            return redirect()->back()->with('error', 'Invalid plan type.');
        }

        $amount = ($billingCycle === 'annually') ? $plan->annual_price : $plan->monthly_price;
        $transactionId = (string) \Illuminate\Support\Str::ulid();
        $redirectUrl = route('payments.callback', ['transaction_id' => $transactionId]);

        $customer = [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => ['country_code' => '966', 'number' => '500000000']
        ];

        try {
            $tapResponse = $this->tapService->createCharge($amount, $customer, $redirectUrl, false, false);
            
            DB::table('transactions')->insert([
                'id' => $transactionId,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'tap_charge_id' => $tapResponse['id'],
                'amount' => $amount,
                'currency' => 'SAR',
                'status' => 'pending',
                'type' => 'subscription',
                'billing_cycle' => $billingCycle,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->away($tapResponse['url']);
        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $tapChargeId = $request->query('tap_id');

        if (!$tapChargeId || !$transactionId) {
            return redirect()->route('client.portfolio')->with('error', 'Invalid response.');
        }

        $transaction = Transaction::find($transactionId);
        if (!$transaction) {
            return redirect()->route('client.portfolio')->with('error', 'Transaction not found.');
        }

        // Idempotency: If already processed, show success page immediately
        if (in_array($transaction->status, ['captured', 'authorized'])) {
            return view('client.checkout.callback', [
                'status' => $transaction->status,
                'message' => 'Payment already processed successfully.',
                'transaction_id' => $transactionId,
                'tap_charge_id' => $transaction->tap_charge_id,
                'project_id' => $transaction->project_id,
                'invoice' => Invoice::where('transaction_id', $transactionId)->first(),
            ]);
        }

        try {
            $charge = $this->tapService->getCharge($tapChargeId);
            $status = strtoupper($charge['status'] ?? 'UNKNOWN');
            
            $mappedStatus = match ($status) {
                'CAPTURED', 'SUCCESS', 'APPROVED' => 'captured',
                'AUTHORIZED' => 'authorized',
                'DECLINED', 'FAILED', 'CANCELLED', 'ABANDONED' => 'failed',
                default => 'pending',
            };

            if (in_array($mappedStatus, ['captured', 'authorized'])) {
                return $this->finalizeTransaction($transaction, $charge, $mappedStatus);
            }

            $transaction->update(['status' => $mappedStatus]);
            return view('client.checkout.callback', [
                'status' => $mappedStatus,
                'message' => 'Payment status: ' . $mappedStatus,
                'transaction_id' => $transactionId,
                'tap_charge_id' => $tapChargeId,
            ]);

        } catch (\Exception $e) {
            Log::error('Callback error: ' . $e->getMessage());
            return view('client.checkout.callback', ['status' => 'error', 'message' => 'Verification failed.', 'transaction_id' => $transactionId]);
        }
    }

    protected function finalizeTransaction($transaction, $charge, $status)
    {
        return DB::transaction(function () use ($transaction, $charge, $status) {
            // Re-check for idempotency inside the transaction
            $transaction = Transaction::where('id', $transaction->id)->lockForUpdate()->first();
            if (in_array($transaction->status, ['captured', 'authorized'])) {
                return view('client.checkout.callback', [
                    'status' => $transaction->status,
                    'message' => 'Payment processed.',
                    'transaction_id' => $transaction->id,
                    'tap_charge_id' => $transaction->tap_charge_id,
                ]);
            }

            $tapCustomerId = $charge['customer']['id'] ?? null;
            $cardToken = $charge['card']['id'] ?? null;

            $transaction->update(['status' => $status]);

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

                Payment::updateOrCreate(
                    ['transaction_id' => $transaction->tap_charge_id],
                    [
                        'user_id' => $user->id,
                        'amount' => $transaction->amount,
                        'payment_method' => 'tap',
                        'status' => 'released',
                        'plan_id' => $transaction->plan_id,
                    ]
                );
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

                Payment::updateOrCreate(
                    ['transaction_id' => $transaction->tap_charge_id],
                    [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'amount' => $transaction->amount,
                        'payment_method' => 'tap',
                        'status' => $status === 'captured' ? 'released' : 'held_in_escrow',
                    ]
                );

                ProjectHistory::create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                    'action' => 'payment_confirmed',
                    'description' => 'Payment confirmed via callback.',
                ]);
            }

            $invoice = $this->invoiceService->generateForTransaction($transaction);
            $invoice->update(['billing_period' => $billingPeriod]);

            return view('client.checkout.callback', [
                'status' => $status,
                'message' => 'Payment successful!',
                'transaction_id' => $transaction->id,
                'tap_charge_id' => $transaction->tap_charge_id,
                'project_id' => $transaction->project_id,
                'invoice' => $invoice,
            ]);
        });
    }
}

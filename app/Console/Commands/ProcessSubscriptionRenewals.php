<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\ProviderService;
use App\Models\Invoice;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-subscription-renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic renewals for active subscriptions';

    protected TapPaymentService $tapService;

    public function __construct(TapPaymentService $tapService)
    {
        parent::__construct();
        $this->tapService = $tapService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::where('status', 'active')
            ->where('next_billing_date', '<=', now())
            ->whereNotNull('card_token')
            ->whereNotNull('tap_customer_id')
            ->get();

        $this->info("Found " . $subscriptions->count() . " subscriptions due for renewal.");

        foreach ($subscriptions as $subscription) {
            $this->processRenewal($subscription);
        }
    }

    protected function processRenewal(Subscription $subscription)
    {
        $amount = 0;
        $description = "";

        if ($subscription->plan_id) {
            $plan = Plan::find($subscription->plan_id);
            if (!$plan) return;
            $amount = ($subscription->billing_cycle === 'annually') ? $plan->annual_price : $plan->monthly_price;
            $description = "Renewal: Platform Plan - " . $plan->name;
        } elseif ($subscription->service_id) {
            $providerService = ProviderService::where('provider_id', $subscription->provider_id)
                ->where('service_id', $subscription->service_id)
                ->first();
            if (!$providerService) return;
            $amount = ($subscription->billing_cycle === 'annually') ? $providerService->annual_price : $providerService->monthly_price;
            $description = "Renewal: Service Subscription - " . ($subscription->service->name ?? 'Service');
        }

        if ($amount <= 0) {
            Log::warning("Skipping renewal for subscription {$subscription->id} due to zero amount.");
            return;
        }

        $this->info("Processing renewal for Subscription #{$subscription->id} - Amount: {$amount} SAR");

        DB::beginTransaction();
        try {
            $response = $this->tapService->chargeSavedCard(
                (float) $amount,
                $subscription->card_token,
                $subscription->tap_customer_id,
                $description
            );

            if (isset($response['status']) && $response['status'] === 'CAPTURED') {
                $transactionId = (string) Str::ulid();
                $tapChargeId = $response['id'];

                // Record Transaction
                DB::table('transactions')->insert([
                    'id' => $transactionId,
                    'user_id' => $subscription->client_id,
                    'plan_id' => $subscription->plan_id,
                    'amount' => $amount,
                    'currency' => 'SAR',
                    'status' => 'captured',
                    'type' => $subscription->plan_id ? 'subscription' : 'service_escrow',
                    'billing_cycle' => $subscription->billing_cycle,
                    'tap_charge_id' => $tapChargeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update Subscription
                $currentNextBilling = $subscription->next_billing_date ?? now();
                $newNextBillingDate = ($subscription->billing_cycle === 'annually') ? $currentNextBilling->copy()->addYear() : $currentNextBilling->copy()->addMonth();
                $billingPeriod = $currentNextBilling->format('M Y') . ' - ' . $newNextBillingDate->format('M Y');

                $subscription->update([
                    'starts_at' => $currentNextBilling,
                    'ends_at' => $newNextBillingDate,
                    'next_billing_date' => $newNextBillingDate,
                ]);

                // Create Invoice
                $invoiceNumber = 'INV-' . strtoupper(Str::random(8));
                Invoice::create([
                    'transaction_id' => $transactionId,
                    'invoice_number' => $invoiceNumber,
                    'billing_period' => $billingPeriod,
                    'billing_details' => [
                        'amount' => $amount,
                        'currency' => 'SAR',
                        'date' => now()->toDateTimeString(),
                        'cycle' => $subscription->billing_cycle,
                    ],
                ]);

                DB::commit();
                $this->info("Successfully renewed Subscription #{$subscription->id}");
            } else {
                DB::rollBack();
                $status = $response['status'] ?? 'UNKNOWN';
                $this->error("Failed to renew Subscription #{$subscription->id}: Status is " . $status);
                // Handle failure (e.g. notify user, mark as past due)
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Renewal failed for subscription {$subscription->id}: " . $e->getMessage());
            $this->error("Error renewing Subscription #{$subscription->id}: " . $e->getMessage());
        }
    }
}

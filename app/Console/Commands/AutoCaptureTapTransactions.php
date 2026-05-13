<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Settings\PaymentSettings;
use Modules\Payments\Services\TapPaymentService;
use Carbon\Carbon;

class AutoCaptureTapTransactions extends Command
{
    protected $signature = 'tap:auto-capture';
    protected $description = 'Automatically capture authorized funds after a configurable delay';

    public function handle(TapPaymentService $tapService, PaymentSettings $settings)
    {
        $delayDays = $settings->auto_capture_days;
        $threshold = Carbon::now()->subDays($delayDays);

        $transactions = Transaction::where('status', 'authorized')
            ->where('created_at', '<=', $threshold)
            ->get();

        $this->info("Found " . $transactions->count() . " authorized transactions ready for auto-capture.");

        foreach ($transactions as $transaction) {
            try {
                $tapService->captureAuthorizedFunds($transaction->tap_charge_id, $transaction->amount);
                $transaction->update(['status' => 'captured']);
                
                if ($transaction->project_id) {
                    \Illuminate\Support\Facades\DB::table('payments')
                        ->where('transaction_id', $transaction->tap_charge_id)
                        ->update(['status' => 'released']);
                }

                $this->line("Auto-captured TXN {$transaction->id}");
            } catch (\Exception $e) {
                $this->error("Failed to auto-capture TXN {$transaction->id}: " . $e->getMessage());
            }
        }

        $this->info('Auto-capture process completed.');
    }
}

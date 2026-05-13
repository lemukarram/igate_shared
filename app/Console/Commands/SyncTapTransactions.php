<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\Log;

class SyncTapTransactions extends Command
{
    protected $signature = 'tap:sync-status';
    protected $description = 'Sync pending/authorized transaction statuses with Tap API';

    public function handle(TapPaymentService $tapService)
    {
        $transactions = Transaction::whereIn('status', ['pending', 'authorized'])->get();

        $this->info("Found " . $transactions->count() . " transactions to sync.");

        foreach ($transactions as $transaction) {
            try {
                $charge = $tapService->getCharge($transaction->tap_charge_id);
                $tapStatus = strtolower($charge['status'] ?? '');

                $mappedStatus = match ($tapStatus) {
                    'captured' => 'captured',
                    'authorized' => 'authorized',
                    'declined', 'failed', 'cancelled' => 'failed',
                    'refunded' => 'refunded',
                    default => $transaction->status,
                };

                if ($mappedStatus !== $transaction->status) {
                    $transaction->update(['status' => $mappedStatus]);
                    $this->line("Updated TXN {$transaction->id} to {$mappedStatus}");

                    // Auto-activate or update project logic
                    if ($transaction->project_id && in_array($mappedStatus, ['captured', 'authorized'])) {
                        $project = $transaction->project;
                        if ($project->status === 'pending_payment') {
                            $project->update(['status' => 'awaiting_approval']);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync TXN {$transaction->id}: " . $e->getMessage());
            }
        }

        $this->info('Sync completed.');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Project;
use App\Traits\SyncsProjectPayment;

class SyncTapTransactions extends Command
{
    use SyncsProjectPayment;

    protected $signature = 'tap:sync-status';
    protected $description = 'Sync pending/authorized transaction statuses with Tap API';

    public function handle()
    {
        $transactions = Transaction::whereIn('status', ['pending', 'authorized'])->get();

        $this->info("Found " . $transactions->count() . " transactions to sync.");

        foreach ($transactions as $transaction) {
            if ($transaction->project) {
                $this->syncProjectPayment($transaction->project);
                $this->line("Synced Project for TXN {$transaction->id}");
            }
        }

        $this->info('Sync completed.');
    }
}

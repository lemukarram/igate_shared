<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\Transaction;
use App\Models\ProjectHistory;
use App\Models\Payment;
use Modules\Payments\Services\TapPaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

trait SyncsProjectPayment
{
    /**
     * Sync project and transaction status with Tap.
     *
     * @param Project $project
     * @return Transaction|null
     */
    public function syncProjectPayment(Project $project)
    {
        $transaction = Transaction::where('project_id', $project->id)->first();
        
        if (!$transaction || !$transaction->tap_charge_id) {
            return $transaction;
        }

        try {
            $tapService = app(TapPaymentService::class);
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
            }

            // Always ensure project status is consistent with payment status
            if (in_array($mappedStatus, ['authorized', 'captured'])) {
                if ($project->status === 'pending_payment') {
                    $project->update(['status' => 'active']);
                    
                    ProjectHistory::create([
                        'project_id' => $project->id,
                        'user_id' => Auth::id() ?? $transaction->user_id,
                        'action' => 'payment_synced',
                        'description' => "Payment status verified as {$mappedStatus}. Project activated.",
                    ]);

                    // Ensure payment record exists for escrow/ledger
                    Payment::updateOrCreate(
                        ['transaction_id' => $transaction->tap_charge_id],
                        [
                            'project_id' => $project->id,
                            'user_id' => $transaction->user_id,
                            'amount' => $transaction->amount,
                            'payment_method' => 'tap',
                            'status' => $mappedStatus === 'captured' ? 'released' : 'held_in_escrow',
                        ]
                    );
                }
            } elseif ($mappedStatus === 'failed') {
                if ($project->status === 'pending_payment') {
                    $project->update(['status' => 'inactive']);
                    
                    ProjectHistory::create([
                        'project_id' => $project->id,
                        'user_id' => Auth::id() ?? $transaction->user_id,
                        'action' => 'payment_failed',
                        'description' => "Payment failed via Tap. Project set to inactive.",
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Tap Sync Error for Project ' . $project->id . ': ' . $e->getMessage());
        }

        return $transaction;
    }
}

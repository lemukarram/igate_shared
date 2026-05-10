<?php

namespace Modules\Admin\Filament\Resources\TransactionResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Payment;
use App\Models\EscrowLedger;

class FinancialStats extends BaseWidget
{
    protected function getStats(): array
    {
        // Simple mock calculations for now, adapt based on real business logic
        $totalRevenue = Payment::where('status', 'released')->sum('amount') * 0.10; // Assuming 10% fee
        $escrowHolding = EscrowLedger::where('type', 'credit')->sum('amount') - EscrowLedger::where('type', 'debit')->sum('amount');
        $pendingPayouts = Payment::where('status', 'held_in_escrow')->sum('amount');

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Platform fee collected')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Escrow Holding', '$' . number_format($escrowHolding, 2))
                ->description('Funds currently held securely')
                ->color('info'),
            Stat::make('Pending Payouts', '$' . number_format($pendingPayouts, 2))
                ->description('Awaiting provider withdrawal')
                ->color('warning'),
        ];
    }
}

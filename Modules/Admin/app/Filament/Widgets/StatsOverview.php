<?php

namespace Modules\Admin\Filament\Widgets;

use App\Models\Project;
use App\Models\User;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Revenue', 'SAR ' . number_format(Payment::sum('amount'), 2))
                ->description('Gross Platform Volume')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Active Projects', Project::where('status', 'active')->count())
                ->description('Ongoing Business Engagements')
                ->descriptionIcon('heroicon-m-briefcase')
                ->chart([1, 5, 2, 8, 4, 12, 10])
                ->color('info'),
            Stat::make('Total Providers', User::where('role', 'provider')->count())
                ->description('Verified Agencies & Freelancers')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Upcoming Renewals', \App\Models\Subscription::where('status', 'active')->where('next_billing_date', '<=', now()->addDays(7))->count())
                ->description('Renewals in next 7 days')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
            Stat::make('Churn Rate', function() {
                $totalActive = \App\Models\Subscription::where('status', 'active')->count();
                $cancelled = \App\Models\Subscription::where('status', 'cancelled')
                    ->where('updated_at', '>=', now()->subMonth())
                    ->count();
                if ($totalActive === 0) return '0%';
                return number_format(($cancelled / ($totalActive + $cancelled)) * 100, 1) . '%';
            })
                ->description('Monthly Churn (Prototype logic)')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
}

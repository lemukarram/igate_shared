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
            Stat::make('Pending Requests', \App\Models\ReleaseRequest::where('status', 'pending')->count())
                ->description('Awaiting Fund Release')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}

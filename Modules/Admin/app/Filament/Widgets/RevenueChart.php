<?php

namespace Modules\Admin\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RevenueChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Monthly Revenue';

    protected function getData(): array
    {
        // Simple data retrieval if Flowframe/Trend is not available
        $data = Payment::where('status', 'released')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, sum(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (SAR)',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

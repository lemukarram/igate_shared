<?php

namespace Modules\Admin\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Projects by Status';

    protected function getData(): array
    {
        $data = Project::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#94a3b8', // pending - gray
                        '#3b82f6', // active - blue
                        '#22c55e', // completed - green
                        '#ef4444', // disputed - red
                        '#f59e0b', // cancelled - yellow
                    ],
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

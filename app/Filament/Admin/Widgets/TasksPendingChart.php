<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksPendingChart extends ChartWidget
{
    protected static ?string $heading = 'Tasks by Status';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $statusCounts = Task::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        '#f59e0b', // pending - yellow
                        '#3b82f6', // in_progress - blue  
                        '#10b981', // completed - green
                        '#ef4444', // cancelled - red
                    ],
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statusCounts)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
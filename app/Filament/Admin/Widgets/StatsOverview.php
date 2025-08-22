<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Client;
use App\Models\CaseModel;
use App\Models\Task;
use App\Models\TimeEntry;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Clients', Client::count())
                ->description('Active: ' . Client::where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Active Cases', CaseModel::whereNotIn('status', ['closed'])->count())
                ->description('Total: ' . CaseModel::count())
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('Pending Tasks', Task::whereIn('status', ['pending', 'in_progress'])->count())
                ->description('Overdue: ' . Task::where('due_date', '<', now())->whereNotIn('status', ['completed', 'cancelled'])->count())
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),

            Stat::make('This Month Billable Hours', 
                number_format(
                    TimeEntry::where('billable', true)
                        ->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->sum('hours'), 
                    1
                ) . ' hrs'
            )
                ->description('Total Amount: $' . number_format(
                    TimeEntry::where('billable', true)
                        ->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->sum('total_amount'), 
                    2
                ))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
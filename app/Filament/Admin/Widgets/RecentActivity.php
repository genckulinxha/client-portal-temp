<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivity extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::with([
                        'assignedToUser:id,name', 
                        'assignedToClient:id,first_name,last_name',
                        'case:id,case_number', 
                        'createdByUser:id,name'
                    ])
                    ->select(['id', 'type', 'title', 'status', 'priority', 'created_at', 'assigned_to_user_id', 'assigned_to_client_id', 'case_id', 'created_by_user_id'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'internal_task',
                        'warning' => 'client_task',
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->weight('bold')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->limit(20),
                Tables\Columns\TextColumn::make('assignedToUser.name')
                    ->label('Assigned To')
                    ->placeholder('Client Task'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'gray' => 'low',
                        'info' => 'medium',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->heading('Recent Tasks')
            ->description('Latest task activity')
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Task $record) => route('filament.admin.resources.tasks.edit', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
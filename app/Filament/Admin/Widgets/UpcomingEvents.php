<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CalendarEvent;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEvents extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CalendarEvent::with(['user', 'case', 'client'])
                    ->where('start_datetime', '>', now())
                    ->where('status', 'scheduled')
                    ->orderBy('start_datetime')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'danger' => 'court_hearing',
                        'warning' => 'deposition',
                        'success' => 'consultation',
                        'primary' => 'meeting',
                        'gray' => 'deadline',
                    ]),
                Tables\Columns\TextColumn::make('start_datetime')
                    ->label('Date & Time')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned To'),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case'),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('location')
                    ->limit(30)
                    ->placeholder('No location'),
            ])
            ->heading('Upcoming Events')
            ->description('Next 10 scheduled events')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (CalendarEvent $record) => route('filament.admin.resources.calendar-events.edit', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}
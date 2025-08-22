<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Case Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('case_id')
                                    ->relationship('case', 'case_number')
                                    ->getOptionLabelFromRecordUsing(fn (LegalCase $record): string => "{$record->case_number} - {$record->title}")
                                    ->searchable(['case_number', 'title'])
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, $state) => 
                                        $set('client_id', LegalCase::find($state)?->client_id)
                                    ),
                                Forms\Components\Select::make('client_id')
                                    ->relationship('client', 'email')
                                    ->getOptionLabelFromRecordUsing(fn (Client $record): string => "{$record->full_name}")
                                    ->searchable(['first_name', 'last_name', 'email'])
                                    ->required()
                                    ->disabled(fn (callable $get) => filled($get('case_id'))),
                            ]),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('task_type')
                                    ->options([
                                        'client_task' => 'Client Task',
                                        'attorney_task' => 'Attorney Task',
                                        'paralegal_task' => 'Paralegal Task',
                                        'deadline' => 'Legal Deadline',
                                        'document_upload' => 'Document Upload',
                                        'review_task' => 'Review Task',
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                        'overdue' => 'Overdue',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                Forms\Components\Select::make('priority')
                                    ->options([
                                        0 => 'Low',
                                        1 => 'Medium',
                                        2 => 'High',
                                    ])
                                    ->default(0)
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('assigned_user_id')
                                    ->relationship('assignedUser', 'name')
                                    ->searchable()
                                    ->label('Assigned To'),
                                Forms\Components\DateTimePicker::make('due_date')
                                    ->label('Due Date'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('estimated_hours')
                                    ->numeric()
                                    ->step(0.1)
                                    ->suffix('hours'),
                                Forms\Components\TextInput::make('actual_hours')
                                    ->numeric()
                                    ->step(0.1)
                                    ->suffix('hours'),
                            ]),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->visible(fn (callable $get) => $get('status') === 'completed'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(['clients.first_name', 'clients.last_name'])
                    ->sortable(),
                Tables\Columns\SelectColumn::make('task_type')
                    ->options([
                        'client_task' => 'Client Task',
                        'attorney_task' => 'Attorney Task',
                        'paralegal_task' => 'Paralegal Task',
                        'deadline' => 'Legal Deadline',
                        'document_upload' => 'Document Upload',
                        'review_task' => 'Review Task',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Low',
                        1 => 'Medium',
                        2 => 'High',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'gray',
                        1 => 'warning',
                        2 => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn (Task $record): string => 
                        $record->is_overdue ? 'danger' : 'gray'
                    ),
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->suffix(' hrs')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('task_type')
                    ->options([
                        'client_task' => 'Client Tasks',
                        'attorney_task' => 'Attorney Tasks',
                        'paralegal_task' => 'Paralegal Tasks',
                        'deadline' => 'Legal Deadlines',
                        'document_upload' => 'Document Uploads',
                        'review_task' => 'Review Tasks',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_user_id')
                    ->relationship('assignedUser', 'name')
                    ->label('Assigned To'),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        0 => 'Low Priority',
                        1 => 'Medium Priority',
                        2 => 'High Priority',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn ($query) => $query->overdue())
                    ->label('Overdue Tasks'),
            ])
            ->actions([
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Task $record) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    })
                    ->visible(fn (Task $record) => !in_array($record->status, ['completed', 'cancelled'])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'completed',
                                    'completed_at' => now(),
                                ]);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('due_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['case', 'client', 'assignedUser']);
    }
}
<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TaskResource\Pages;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\CaseModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->options([
                                'client_task' => 'Client Task',
                                'internal_task' => 'Internal Task',
                            ])
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('assigned_to_user_id')
                            ->label('Assign to User')
                            ->relationship('assignedToUser', 'name')
                            ->options(User::pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('assigned_to_client_id')
                            ->label('Assign to Client')
                            ->relationship('assignedToClient', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => "{$record->full_name} ({$record->email})")
                            ->searchable(['first_name', 'last_name', 'email']),
                        Forms\Components\Select::make('case_id')
                            ->relationship('case', 'case_title')
                            ->searchable(['case_number', 'case_title']),
                        Forms\Components\DateTimePicker::make('due_date'),
                    ])->columns(2),

                Forms\Components\Section::make('Requirements & Completion')
                    ->schema([
                        Forms\Components\KeyValue::make('requirements')
                            ->label('Task Requirements'),
                        Forms\Components\Textarea::make('completion_notes')
                            ->rows(3),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'client_task',
                        'secondary' => 'internal_task',
                    ]),
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
                Tables\Columns\TextColumn::make('assignedToUser.name')
                    ->label('Assigned User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedToClient.full_name')
                    ->label('Assigned Client')
                    ->sortable(),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->color(fn (Task $record) => $record->is_overdue ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'client_task' => 'Client Task',
                        'internal_task' => 'Internal Task',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query) => $query->where('due_date', '<', now())
                        ->whereNotIn('status', ['completed', 'cancelled'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Task $record) => !in_array($record->status, ['completed', 'cancelled']))
                    ->action(fn (Task $record) => $record->markCompleted()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
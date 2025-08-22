<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TimeEntryResource\Pages;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\CaseModel;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Calendar & Time';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Time Entry';

    protected static ?string $pluralLabel = 'Time Entries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Time Entry Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(today()),
                        Forms\Components\TextInput::make('hours')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0.1)
                            ->maxValue(24)
                            ->suffix('hours')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Time Details')
                    ->schema([
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Start Time'),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('End Time')
                            ->after('start_time'),
                    ])->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('case_id')
                            ->label('Case')
                            ->relationship('case', 'case_title')
                            ->searchable(['case_number', 'case_title'])
                            ->preload(),
                        Forms\Components\Select::make('task_id')
                            ->label('Task')
                            ->relationship('task', 'title')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Billing Information')
                    ->schema([
                        Forms\Components\Toggle::make('billable')
                            ->label('Billable')
                            ->default(true),
                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('Hourly Rate')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->helperText('Leave blank to use user default rate'),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->disabled()
                            ->helperText('Calculated automatically'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'billed' => 'Billed',
                            ])
                            ->required()
                            ->default('draft'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Additional Data'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->sortable(),
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Task')
                    ->limit(30)
                    ->sortable(),
                Tables\Columns\TextColumn::make('hours')
                    ->suffix(' hrs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('billable')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'submitted',
                        'success' => 'approved',
                        'primary' => 'billed',
                    ]),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User'),
                Tables\Filters\SelectFilter::make('case_id')
                    ->relationship('case', 'case_number')
                    ->label('Case'),
                Tables\Filters\TernaryFilter::make('billable'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'submitted' => 'Submitted',
                        'approved' => 'Approved',
                        'billed' => 'Billed',
                    ]),
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date'),
                        Forms\Components\DatePicker::make('to_date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (TimeEntry $record) => $record->status === 'draft')
                    ->action(fn (TimeEntry $record) => $record->update(['status' => 'submitted'])),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TimeEntry $record) => $record->status === 'submitted' && auth()->user()->isManagingPartner())
                    ->action(fn (TimeEntry $record) => $record->update(['status' => 'approved'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('submit')
                        ->label('Submit Selected')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['status' => 'submitted'])),
                ]),
            ])
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListTimeEntries::route('/'),
            'create' => Pages\CreateTimeEntry::route('/create'),
            'edit' => Pages\EditTimeEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'case', 'task']);
    }
}
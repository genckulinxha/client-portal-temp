<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CalendarEventResource\Pages;
use App\Models\CalendarEvent;
use App\Models\User;
use App\Models\Client;
use App\Models\CaseModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Calendar & Time';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Calendar Event';

    protected static ?string $pluralLabel = 'Calendar Events';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->options([
                                'meeting' => 'Meeting',
                                'court_hearing' => 'Court Hearing',
                                'deposition' => 'Deposition',
                                'consultation' => 'Consultation',
                                'deadline' => 'Deadline',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'rescheduled' => 'Rescheduled',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Date & Time')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_datetime')
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_datetime')
                            ->required()
                            ->after('start_datetime'),
                        Forms\Components\Toggle::make('all_day')
                            ->label('All Day Event'),
                    ])->columns(3),

                Forms\Components\Section::make('Assignment & Relationships')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Assigned To')
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\Select::make('case_id')
                            ->label('Related Case')
                            ->relationship('case', 'case_title')
                            ->searchable(['case_number', 'case_title']),
                        Forms\Components\Select::make('client_id')
                            ->label('Related Client')
                            ->relationship('client', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => "{$record->full_name} ({$record->email})")
                            ->searchable(['first_name', 'last_name', 'email']),
                    ])->columns(3),

                Forms\Components\Section::make('Location & Meeting Details')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Meeting Link (Zoom, Teams, etc.)')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('attendees')
                            ->label('Attendee Emails')
                            ->placeholder('Add email addresses'),
                    ])->columns(2),

                Forms\Components\Section::make('Google Calendar Integration')
                    ->schema([
                        Forms\Components\TextInput::make('google_event_id')
                            ->label('Google Event ID')
                            ->disabled(),
                        Forms\Components\Toggle::make('synced_with_google')
                            ->label('Synced with Google Calendar')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('last_google_sync')
                            ->label('Last Google Sync')
                            ->disabled(),
                    ])->columns(3)
                    ->collapsed(),

                Forms\Components\Section::make('Reminders')
                    ->schema([
                        Forms\Components\KeyValue::make('reminders')
                            ->label('Reminder Settings')
                            ->keyLabel('Reminder Type')
                            ->valueLabel('Minutes Before'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('start_datetime', '>', now())
            ->where('status', 'scheduled')
            ->count();
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
                        'primary' => 'meeting',
                        'danger' => 'court_hearing',
                        'warning' => 'deposition',
                        'success' => 'consultation',
                        'gray' => 'deadline',
                        'secondary' => 'other',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'scheduled',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'info' => 'rescheduled',
                    ]),
                Tables\Columns\TextColumn::make('start_datetime')
                    ->label('Start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_datetime')
                    ->label('End')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->sortable(),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('synced_with_google')
                    ->label('Google Sync')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'meeting' => 'Meeting',
                        'court_hearing' => 'Court Hearing',
                        'deposition' => 'Deposition',
                        'consultation' => 'Consultation',
                        'deadline' => 'Deadline',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'rescheduled' => 'Rescheduled',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Assigned To'),
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn (Builder $query) => $query->where('start_datetime', '>', now()))
                    ->label('Upcoming Events'),
                Tables\Filters\Filter::make('today')
                    ->query(fn (Builder $query) => $query->whereDate('start_datetime', today()))
                    ->label('Today'),
                Tables\Filters\Filter::make('this_week')
                    ->query(fn (Builder $query) => $query->whereBetween('start_datetime', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]))
                    ->label('This Week'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CalendarEvent $record) => $record->status === 'scheduled')
                    ->action(fn (CalendarEvent $record) => $record->update(['status' => 'completed'])),
                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CalendarEvent $record) => $record->status === 'scheduled')
                    ->requiresConfirmation()
                    ->action(fn (CalendarEvent $record) => $record->update(['status' => 'cancelled'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_datetime', 'asc');
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
            'index' => Pages\ListCalendarEvents::route('/'),
            'create' => Pages\CreateCalendarEvent::route('/create'),
            'edit' => Pages\EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
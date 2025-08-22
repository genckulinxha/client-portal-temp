<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bar_number')
                            ->label('Bar Number')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bio')
                            ->rows(3)
                            ->maxLength(1000),
                    ])->columns(2),

                Forms\Components\Section::make('Role & Status')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->options([
                                'managing_partner' => 'Managing Partner',
                                'attorney' => 'Attorney',
                                'paralegal' => 'Paralegal',
                                'intake_team' => 'Intake Team',
                                'admin' => 'Admin',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('active')
                            ->label('Active User')
                            ->default(true),
                        Forms\Components\TextInput::make('hourly_rate')
                            ->label('Hourly Rate')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$'),
                    ])->columns(3),

                Forms\Components\Section::make('Security')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->same('password')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(false),
                    ])->columns(2),

                Forms\Components\Section::make('Permissions')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->options([
                                'manage_cases' => 'Manage Cases',
                                'manage_clients' => 'Manage Clients',
                                'manage_documents' => 'Manage Documents',
                                'manage_calendar' => 'Manage Calendar',
                                'manage_time_entries' => 'Manage Time Entries',
                                'manage_billing' => 'Manage Billing',
                                'view_reports' => 'View Reports',
                                'manage_users' => 'Manage Users',
                            ])
                            ->columns(2)
                            ->helperText('Managing Partners and Admins have all permissions automatically'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'managing_partner',
                        'success' => 'attorney',
                        'warning' => 'paralegal',
                        'info' => 'intake_team',
                        'secondary' => 'admin',
                    ]),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bar_number')
                    ->label('Bar #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('attorneyCases_count')
                    ->label('Cases (Attorney)')
                    ->counts('attorneyCases'),
                Tables\Columns\TextColumn::make('paralegalCases_count')
                    ->label('Cases (Paralegal)')
                    ->counts('paralegalCases'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'managing_partner' => 'Managing Partner',
                        'attorney' => 'Attorney',
                        'paralegal' => 'Paralegal',
                        'intake_team' => 'Intake Team',
                        'admin' => 'Admin',
                    ]),
                Tables\Filters\TernaryFilter::make('active'),
                Tables\Filters\Filter::make('has_bar_number')
                    ->query(fn (Builder $query) => $query->whereNotNull('bar_number'))
                    ->label('Has Bar Number'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (User $record) => $record->active ? 'Deactivate' : 'Activate')
                    ->icon(fn (User $record) => $record->active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (User $record) => $record->active ? 'danger' : 'success')
                    ->action(fn (User $record) => $record->update(['active' => !$record->active])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['active' => false])),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['attorneyCases', 'paralegalCases']);
    }
}
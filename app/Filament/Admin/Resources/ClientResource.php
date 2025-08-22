<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Client Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->regex('/^[\+]?[1-9][\d]{0,15}$/')
                            ->helperText('Enter phone number with country code (e.g., +1234567890)'),
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\TextInput::make('ssn')
                            ->label('SSN')
                            ->password()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('state')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Portal Access')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'prospect' => 'Prospect',
                                'active' => 'Active',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('portal_access')
                            ->label('Portal Access Enabled')
                            ->reactive(),
                        Forms\Components\TextInput::make('portal_password')
                            ->password()
                            ->visible(fn (Forms\Get $get) => $get('portal_access'))
                            ->required(fn (Forms\Get $get) => $get('portal_access'))
                            ->helperText('Password for client portal login'),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                        Forms\Components\KeyValue::make('intake_data')
                            ->label('Intake Data'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'prospect',
                        'success' => 'active',
                        'danger' => 'closed',
                    ]),
                Tables\Columns\IconColumn::make('portal_access')
                    ->boolean(),
                Tables\Columns\TextColumn::make('cases_count')
                    ->counts('cases')
                    ->label('Cases'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active' => 'Active',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\TernaryFilter::make('portal_access'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('cases');
    }
}
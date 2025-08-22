<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

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
                Forms\Components\Section::make('Client Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('address_line_1')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address_line_2')
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('state')
                                    ->required()
                                    ->maxLength(2)
                                    ->placeholder('CA'),
                                Forms\Components\TextInput::make('zip_code')
                                    ->required()
                                    ->maxLength(10),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth'),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'prospect' => 'Prospect',
                                        'active' => 'Active',
                                        'closed' => 'Closed',
                                    ])
                                    ->default('prospect')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('referral_source')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('lead_source_url')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('intake_completed_at')
                                    ->label('Intake Completed'),
                                Forms\Components\DateTimePicker::make('retainer_signed_at')
                                    ->label('Retainer Signed'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active' => 'Active',
                        'closed' => 'Closed',
                    ])
                    ->sortable(),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Client Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('first_name'),
                                Infolists\Components\TextEntry::make('last_name'),
                                Infolists\Components\TextEntry::make('email')
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('phone'),
                            ]),
                    ]),
                Infolists\Components\Section::make('Address')
                    ->schema([
                        Infolists\Components\TextEntry::make('address_line_1'),
                        Infolists\Components\TextEntry::make('address_line_2'),
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('city'),
                                Infolists\Components\TextEntry::make('state'),
                                Infolists\Components\TextEntry::make('zip_code'),
                            ]),
                    ]),
                Infolists\Components\Section::make('Case Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'prospect' => 'warning',
                                'active' => 'success',
                                'closed' => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('referral_source'),
                        Infolists\Components\TextEntry::make('intake_completed_at')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('retainer_signed_at')
                            ->dateTime(),
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
            'view' => Pages\ViewClient::route('/{record}'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
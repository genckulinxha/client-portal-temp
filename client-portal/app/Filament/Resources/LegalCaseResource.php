<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LegalCaseResource\Pages;
use App\Models\LegalCase;
use App\Models\Client;
use App\Models\CaseType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class LegalCaseResource extends Resource
{
    protected static ?string $model = LegalCase::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Case Management';

    protected static ?string $navigationLabel = 'Cases';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Case Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->relationship('client', 'email')
                                    ->getOptionLabelFromRecordUsing(fn (Client $record): string => "{$record->full_name} ({$record->email})")
                                    ->searchable(['first_name', 'last_name', 'email'])
                                    ->required(),
                                Forms\Components\Select::make('case_type_id')
                                    ->relationship('caseType', 'name')
                                    ->required(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('case_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn () => 'CASE-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)),
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('status')
                            ->options([
                                'intake' => 'Intake',
                                'investigation' => 'Investigation',
                                'pre_litigation' => 'Pre-Litigation',
                                'active_litigation' => 'Active Litigation',
                                'discovery' => 'Discovery',
                                'settlement_negotiations' => 'Settlement Negotiations',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->default('intake')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('assigned_attorney_id')
                                    ->relationship('assignedAttorney', 'name')
                                    ->options(fn () => User::where('role', 'attorney')->pluck('name', 'id'))
                                    ->searchable(),
                                Forms\Components\Select::make('assigned_paralegal_id')
                                    ->relationship('assignedParalegal', 'name')
                                    ->options(fn () => User::where('role', 'paralegal')->pluck('name', 'id'))
                                    ->searchable(),
                            ]),
                    ]),

                Forms\Components\Section::make('Case Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('damages_category')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date_opened')
                                    ->default(now())
                                    ->required(),
                            ]),
                        Forms\Components\DatePicker::make('date_closed'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('settlement_amount')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('attorney_fees')
                                    ->numeric()
                                    ->prefix('$'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('case_expenses')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('referral_fee_percentage')
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxValue(100),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('case_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(['clients.first_name', 'clients.last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('caseType.name')
                    ->label('Type')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'intake' => 'Intake',
                        'investigation' => 'Investigation',
                        'pre_litigation' => 'Pre-Litigation',
                        'active_litigation' => 'Active Litigation',
                        'discovery' => 'Discovery',
                        'settlement_negotiations' => 'Settlement Negotiations',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedAttorney.name')
                    ->label('Attorney')
                    ->sortable(),
                Tables\Columns\TextColumn::make('settlement_amount')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_opened')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'intake' => 'Intake',
                        'investigation' => 'Investigation',
                        'pre_litigation' => 'Pre-Litigation',
                        'active_litigation' => 'Active Litigation',
                        'discovery' => 'Discovery',
                        'settlement_negotiations' => 'Settlement Negotiations',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_attorney_id')
                    ->relationship('assignedAttorney', 'name')
                    ->label('Attorney'),
                Tables\Filters\SelectFilter::make('case_type_id')
                    ->relationship('caseType', 'name')
                    ->label('Case Type'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalCases::route('/'),
            'create' => Pages\CreateLegalCase::route('/create'),
            'view' => Pages\ViewLegalCase::route('/{record}'),
            'edit' => Pages\EditLegalCase::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CaseResource\Pages;
use App\Models\CaseModel;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CaseResource extends Resource
{
    protected static ?string $model = CaseModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Case Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Case';

    protected static ?string $pluralLabel = 'Cases';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Case Information')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => "{$record->full_name} ({$record->email})")
                            ->searchable(['first_name', 'last_name', 'email'])
                            ->required(),
                        Forms\Components\TextInput::make('case_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('case_title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('case_type')
                            ->options([
                                'fcra_dispute' => 'FCRA Dispute',
                                'fcra_lawsuit' => 'FCRA Lawsuit',
                                'identity_theft' => 'Identity Theft',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'intake' => 'Intake',
                                'investigation' => 'Investigation',
                                'litigation' => 'Litigation',
                                'settlement' => 'Settlement',
                                'closed' => 'Closed',
                            ])
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('attorney_id')
                            ->relationship('attorney', 'name')
                            ->options(User::where('role', 'attorney')->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\Select::make('paralegal_id')
                            ->relationship('paralegal', 'name')
                            ->options(User::where('role', 'paralegal')->pluck('name', 'id')),
                    ])->columns(2),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\TextInput::make('potential_damages')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('settlement_amount')
                            ->numeric()
                            ->prefix('$'),
                    ])->columns(2),

                Forms\Components\Section::make('Important Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('statute_limitations')
                            ->label('Statute of Limitations'),
                        Forms\Components\DatePicker::make('filed_date'),
                        Forms\Components\DatePicker::make('closed_date'),
                    ])->columns(3),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Additional Data'),
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
                Tables\Columns\TextColumn::make('case_title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(['clients.first_name', 'clients.last_name'])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('case_type')
                    ->colors([
                        'primary' => 'fcra_dispute',
                        'success' => 'fcra_lawsuit',
                        'warning' => 'identity_theft',
                        'secondary' => 'other',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'intake',
                        'warning' => 'investigation',
                        'danger' => 'litigation',
                        'success' => 'settlement',
                        'secondary' => 'closed',
                    ]),
                Tables\Columns\TextColumn::make('attorney.name')
                    ->label('Attorney')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paralegal.name')
                    ->label('Paralegal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('potential_damages')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'intake' => 'Intake',
                        'investigation' => 'Investigation',
                        'litigation' => 'Litigation',
                        'settlement' => 'Settlement',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('case_type')
                    ->options([
                        'fcra_dispute' => 'FCRA Dispute',
                        'fcra_lawsuit' => 'FCRA Lawsuit',
                        'identity_theft' => 'Identity Theft',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('attorney_id')
                    ->relationship('attorney', 'name')
                    ->label('Attorney'),
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
            'index' => Pages\ListCases::route('/'),
            'create' => Pages\CreateCase::route('/create'),
            'edit' => Pages\EditCase::route('/{record}/edit'),
        ];
    }
}
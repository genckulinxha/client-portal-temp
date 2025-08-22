<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use App\Models\LegalCase;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Client Chat';

    protected static ?string $modelLabel = 'Client Conversation';

    protected static ?string $pluralModelLabel = 'Client Conversations';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('case_id')
                    ->label('Case')
                    ->options(function() {
                        return LegalCase::with('client')->get()->mapWithKeys(function($case) {
                            return [$case->id => $case->case_number . ' - ' . $case->client->full_name];
                        });
                    })
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ])
                    ->default('active')
                    ->required(),

                Forms\Components\Hidden::make('created_by_type')
                    ->default('user'),

                Forms\Components\Hidden::make('created_by_id')
                    ->default(fn () => auth()->id()),

                Forms\Components\Hidden::make('client_id')
                    ->default(function (Forms\Get $get) {
                        $caseId = $get('case_id');
                        if ($caseId) {
                            $case = LegalCase::find($caseId);
                            return $case?->client_id;
                        }
                        return null;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'archived',
                    ]),

                Tables\Columns\TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->since(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ]),

                Tables\Filters\SelectFilter::make('case_id')
                    ->label('Case')
                    ->options(LegalCase::pluck('case_number', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\TextEntry::make('case.case_number')
                        ->label('Case Number'),
                    Infolists\Components\TextEntry::make('client.full_name')
                        ->label('Client'),
                    Infolists\Components\TextEntry::make('subject')
                        ->label('Subject'),
                    Infolists\Components\TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active' => 'success',
                            'archived' => 'warning',
                        }),
                ])->columns(2),
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
            'index' => Pages\ListConversations::route('/'),
            'create' => Pages\CreateConversation::route('/create'),
            'view' => Pages\ViewConversation::route('/{record}'),
            'edit' => Pages\EditConversation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['case', 'client', 'messages']);
    }
}
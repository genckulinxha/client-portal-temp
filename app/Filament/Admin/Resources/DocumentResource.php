<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\Client;
use App\Models\CaseModel;
use App\Models\Task;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationGroup = 'Document Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->directory('documents')
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'text/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'client_document' => 'Client Document',
                                'pleading' => 'Pleading',
                                'discovery' => 'Discovery',
                                'correspondence' => 'Correspondence',
                                'court_filing' => 'Court Filing',
                                'settlement_doc' => 'Settlement Document',
                                'retainer' => 'Retainer',
                                'intake_form' => 'Intake Form',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Access Control')
                    ->schema([
                        Forms\Components\Toggle::make('client_viewable')
                            ->label('Client Can View')
                            ->helperText('Allow the client to view and download this document'),
                        Forms\Components\Toggle::make('is_confidential')
                            ->label('Confidential')
                            ->helperText('Mark as confidential document'),
                    ])->columns(2),

                Forms\Components\Section::make('Relationships')
                    ->schema([
                        Forms\Components\Select::make('case_id')
                            ->label('Case')
                            ->relationship('case', 'case_title')
                            ->searchable(['case_number', 'case_title'])
                            ->preload(),
                        Forms\Components\Select::make('client_id')
                            ->label('Client')
                            ->relationship('client', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Client $record) => "{$record->full_name} ({$record->email})")
                            ->searchable(['first_name', 'last_name', 'email'])
                            ->preload(),
                        Forms\Components\Select::make('task_id')
                            ->label('Related Task')
                            ->relationship('task', 'title')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('uploaded_by_user_id')
                            ->label('Uploaded by User')
                            ->relationship('uploadedByUser', 'name')
                            ->default(auth()->id()),
                    ])->columns(2),

                Forms\Components\Section::make('Version Control')
                    ->schema([
                        Forms\Components\TextInput::make('version')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\Select::make('parent_document_id')
                            ->label('Parent Document (for versions)')
                            ->relationship('parentDocument', 'title')
                            ->searchable(),
                    ])->columns(2)
                    ->collapsed(),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Document Metadata'),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'client_document',
                        'success' => 'pleading',
                        'warning' => 'discovery',
                        'info' => 'correspondence',
                        'danger' => 'court_filing',
                        'secondary' => 'other',
                    ]),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->sortable(),
                Tables\Columns\IconColumn::make('client_viewable')
                    ->boolean()
                    ->label('Client Access'),
                Tables\Columns\IconColumn::make('is_confidential')
                    ->boolean()
                    ->label('Confidential'),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 1) . ' KB' : 'N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploadedByUser.name')
                    ->label('Uploaded By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'client_document' => 'Client Document',
                        'pleading' => 'Pleading',
                        'discovery' => 'Discovery',
                        'correspondence' => 'Correspondence',
                        'court_filing' => 'Court Filing',
                        'settlement_doc' => 'Settlement Document',
                        'retainer' => 'Retainer',
                        'intake_form' => 'Intake Form',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('case_id')
                    ->relationship('case', 'case_number')
                    ->label('Case'),
                Tables\Filters\TernaryFilter::make('client_viewable')
                    ->label('Client Viewable'),
                Tables\Filters\TernaryFilter::make('is_confidential')
                    ->label('Confidential'),
                Tables\Filters\SelectFilter::make('uploaded_by_user_id')
                    ->relationship('uploadedByUser', 'name')
                    ->label('Uploaded By'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record) => route('client.documents.download', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_client_access')
                        ->label('Toggle Client Access')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each(fn ($record) => $record->update(['client_viewable' => !$record->client_viewable]))),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['case', 'client', 'task', 'uploadedByUser']);
    }
}
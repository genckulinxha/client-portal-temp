<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\LegalCase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Case Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('case_id')
                                    ->relationship('case', 'case_number')
                                    ->getOptionLabelFromRecordUsing(fn (LegalCase $record): string => "{$record->case_number} - {$record->title}")
                                    ->searchable(['case_number', 'title'])
                                    ->required(),
                                Forms\Components\Select::make('defendant_id')
                                    ->relationship('defendant', 'name')
                                    ->label('Related Defendant (Optional)'),
                            ]),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'Credit Reports' => 'Credit Reports',
                                'Client Documents' => 'Client Documents',
                                'Pleadings' => 'Pleadings',
                                'Discovery' => 'Discovery',
                                'Correspondence' => 'Correspondence',
                                'Court Orders' => 'Court Orders',
                                'Settlement Documents' => 'Settlement Documents',
                                'Expert Reports' => 'Expert Reports',
                                'Medical Records' => 'Medical Records',
                                'Financial Records' => 'Financial Records',
                            ])
                            ->required()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('File Upload')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->required()
                            ->disk('local')
                            ->directory('case-documents')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'text/plain',
                            ])
                            ->maxSize(50 * 1024) // 50MB
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state, callable $get) {
                                if ($state) {
                                    $filename = is_array($state) ? $state[0] : $state;
                                    if (!$get('title')) {
                                        $set('title', pathinfo($filename, PATHINFO_FILENAME));
                                    }
                                }
                            }),
                    ]),

                Forms\Components\Section::make('Document Settings')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_privileged')
                                    ->label('Attorney-Client Privileged')
                                    ->helperText('Mark if this document contains privileged communications'),
                                Forms\Components\Toggle::make('client_accessible')
                                    ->label('Client Portal Access')
                                    ->helperText('Allow client to view this document in their portal'),
                            ]),
                        Forms\Components\TextInput::make('version')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\Select::make('parent_document_id')
                            ->relationship('parentDocument', 'title')
                            ->label('Previous Version (Optional)')
                            ->helperText('Select if this is an updated version of an existing document'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('case.case_number')
                    ->label('Case')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_filename')
                    ->label('File Name')
                    ->limit(25)
                    ->tooltip(fn (Document $record): string => $record->original_filename),
                Tables\Columns\TextColumn::make('file_size_human')
                    ->label('Size')
                    ->sortable('file_size'),
                Tables\Columns\TextColumn::make('uploadedBy.name')
                    ->label('Uploaded By')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_privileged')
                    ->label('Privileged')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),
                Tables\Columns\IconColumn::make('client_accessible')
                    ->label('Client Access')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash'),
                Tables\Columns\TextColumn::make('version')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Credit Reports' => 'Credit Reports',
                        'Client Documents' => 'Client Documents',
                        'Pleadings' => 'Pleadings',
                        'Discovery' => 'Discovery',
                        'Correspondence' => 'Correspondence',
                        'Court Orders' => 'Court Orders',
                        'Settlement Documents' => 'Settlement Documents',
                        'Expert Reports' => 'Expert Reports',
                        'Medical Records' => 'Medical Records',
                        'Financial Records' => 'Financial Records',
                    ]),
                Tables\Filters\TernaryFilter::make('is_privileged')
                    ->label('Privileged Documents'),
                Tables\Filters\TernaryFilter::make('client_accessible')
                    ->label('Client Accessible'),
                Tables\Filters\SelectFilter::make('case_id')
                    ->relationship('case', 'case_number')
                    ->label('Case')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Document $record): string => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_client_accessible')
                        ->label('Make Client Accessible')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['client_accessible' => true]);
                            }
                        }),
                    Tables\Actions\BulkAction::make('mark_privileged')
                        ->label('Mark as Privileged')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_privileged' => true]);
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['case', 'uploadedBy']);
    }
}
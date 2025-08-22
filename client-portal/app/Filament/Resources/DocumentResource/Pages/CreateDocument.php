<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by_user_id'] = auth()->id();
        
        // If file_path is set, extract file information
        if (isset($data['file_path'])) {
            $filePath = $data['file_path'];
            $data['original_filename'] = basename($filePath);
            $data['file_size'] = \Storage::size($filePath);
            $data['mime_type'] = \Storage::mimeType($filePath);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
<?php

namespace App\Filament\Admin\Resources\DocumentResource\Pages;

use App\Filament\Admin\Resources\DocumentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set uploaded by user if not specified
        if (empty($data['uploaded_by_user_id'])) {
            $data['uploaded_by_user_id'] = auth()->id();
        }

        return $data;
    }
}
<?php

namespace App\Filament\Admin\Resources\TimeEntryResource\Pages;

use App\Filament\Admin\Resources\TimeEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeEntry extends CreateRecord
{
    protected static string $resource = TimeEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default user if not specified
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
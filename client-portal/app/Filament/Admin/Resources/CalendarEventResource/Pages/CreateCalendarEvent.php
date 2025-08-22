<?php

namespace App\Filament\Admin\Resources\CalendarEventResource\Pages;

use App\Filament\Admin\Resources\CalendarEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendarEvent extends CreateRecord
{
    protected static string $resource = CalendarEventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default user if not specified
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
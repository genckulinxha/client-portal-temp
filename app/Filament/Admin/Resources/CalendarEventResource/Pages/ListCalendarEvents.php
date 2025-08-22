<?php

namespace App\Filament\Admin\Resources\CalendarEventResource\Pages;

use App\Filament\Admin\Resources\CalendarEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarEvents extends ListRecords
{
    protected static string $resource = CalendarEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
<?php

namespace App\Filament\Admin\Resources\TimeEntryResource\Pages;

use App\Filament\Admin\Resources\TimeEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeEntries extends ListRecords
{
    protected static string $resource = TimeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
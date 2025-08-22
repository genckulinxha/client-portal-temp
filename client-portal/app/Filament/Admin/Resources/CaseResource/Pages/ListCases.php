<?php

namespace App\Filament\Admin\Resources\CaseResource\Pages;

use App\Filament\Admin\Resources\CaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCases extends ListRecords
{
    protected static string $resource = CaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
<?php

namespace App\Filament\Admin\Resources\CaseResource\Pages;

use App\Filament\Admin\Resources\CaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCase extends EditRecord
{
    protected static string $resource = CaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
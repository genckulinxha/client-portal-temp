<?php

namespace App\Filament\Resources\LegalCaseResource\Pages;

use App\Filament\Resources\LegalCaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLegalCase extends CreateRecord
{
    protected static string $resource = LegalCaseResource::class;

    protected function afterCreate(): void
    {
        $record = $this->getRecord();
        \Log::info('Legal case created in admin panel', [
            'case_id' => $record->id,
            'case_number' => $record->case_number,
            'client_id' => $record->client_id,
            'title' => $record->title,
            'status' => $record->status,
            'assigned_attorney_id' => $record->assigned_attorney_id,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
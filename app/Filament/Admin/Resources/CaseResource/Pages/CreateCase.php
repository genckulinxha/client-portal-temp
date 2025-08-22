<?php

namespace App\Filament\Admin\Resources\CaseResource\Pages;

use App\Filament\Admin\Resources\CaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCase extends CreateRecord
{
    protected static string $resource = CaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generate case number if not provided
        if (empty($data['case_number'])) {
            $data['case_number'] = 'CASE-' . now()->format('Y') . '-' . str_pad(
                \App\Models\CaseModel::whereYear('created_at', now()->year)->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        }

        return $data;
    }
}
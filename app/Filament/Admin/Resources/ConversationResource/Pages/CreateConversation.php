<?php

namespace App\Filament\Admin\Resources\ConversationResource\Pages;

use App\Filament\Admin\Resources\ConversationResource;
use App\Models\LegalCase;
use Filament\Resources\Pages\CreateRecord;

class CreateConversation extends CreateRecord
{
    protected static string $resource = ConversationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_type'] = 'user';
        $data['created_by_id'] = auth()->id();
        
        if ($data['case_id']) {
            $case = LegalCase::find($data['case_id']);
            $data['client_id'] = $case->client_id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $conversation = $this->record;
        $case = $conversation->case;

        $conversation->addParticipant('client', $case->client_id);
        
        if ($case->attorney_id) {
            $conversation->addParticipant('user', $case->attorney_id);
        }
        
        if ($case->paralegal_id) {
            $conversation->addParticipant('user', $case->paralegal_id);
        }
    }
}
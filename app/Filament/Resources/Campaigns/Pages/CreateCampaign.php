<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default status
        $data['status'] = 'pending';
        
        // Validate for duplicates
        $this->validateRecipients($data['recipients'] ?? []);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Create campaign recipients from the repeater data
        $recipients = $this->data['recipients'] ?? [];
        
        foreach ($recipients as $recipient) {
            $this->record->recipients()->create([
                'application_id' => $recipient['application_id'],
                'list_id' => $recipient['list_id'],
                'segment_id' => $recipient['segment_id'],
            ]);
        }
    }

    private function validateRecipients(array $recipients): void
    {
        $seen = [];
        
        foreach ($recipients as $recipient) {
            $key = $recipient['application_id'] . '|' . 
                   $recipient['list_id'] . '|' . 
                   $recipient['segment_id'];
            
            if (in_array($key, $seen)) {
                throw new \Exception('Cannot add the same combination of application, list and segment more than once.');
            }
            
            $seen[] = $key;
        }
    }
}

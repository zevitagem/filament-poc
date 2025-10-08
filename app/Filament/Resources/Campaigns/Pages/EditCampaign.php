<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load recipients data for the repeater
        $recipients = $this->record->recipients()->get()->map(function ($recipient) {
            return [
                'application_id' => $recipient->application_id,
                'list_id' => $recipient->list_id,
                'segment_id' => $recipient->segment_id,
            ];
        })->toArray();

        $data['recipients'] = $recipients;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate for duplicates
        $this->validateRecipients($data['recipients'] ?? []);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Update campaign recipients from the repeater data
        $recipients = $this->data['recipients'] ?? [];
        
        // Delete existing recipients
        $this->record->recipients()->delete();
        
        // Create new recipients
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

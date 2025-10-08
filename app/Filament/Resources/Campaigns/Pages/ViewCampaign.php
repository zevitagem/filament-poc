<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Jobs\ProcessCampaignJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('process')
                ->label('Process Campaign')
                ->icon('heroicon-o-play')
                ->color('success')
                ->visible(fn () => $this->record->status === 'pending')
                ->requiresConfirmation()
                ->action(function () {
                    ProcessCampaignJob::dispatch($this->record);
                    
                    Notification::make()
                        ->title('Campaign Processing Started')
                        ->body('The campaign has been queued for processing.')
                        ->success()
                        ->send();
                }),
        ];
    }
}

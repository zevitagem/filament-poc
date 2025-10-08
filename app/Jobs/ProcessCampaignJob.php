<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignRecipient;
use App\Models\Notification;
use App\Services\SpunManagerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCampaignJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Campaign $campaign
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update campaign status to processing
            $this->campaign->update(['status' => 'processing']);

            // Log campaign start
            $this->logCampaign('info', 'Campaign processing started');

            $spunManagerService = new SpunManagerService();
            $recipients = $this->campaign->recipients;
            $successCount = 0;
            $failureCount = 0;

            foreach ($recipients as $recipient) {
                try {
                    $this->processRecipient($recipient, $spunManagerService);
                    $successCount++;
                } catch (\Exception $e) {
                    $failureCount++;
                    $this->logRecipient($recipient, 'error', 'Failed to process recipient: ' . $e->getMessage());
                }
            }

            // Update campaign status based on results
            $finalStatus = $failureCount > 0 ? 'failed' : 'sent';
            $this->campaign->update(['status' => $finalStatus]);

            // Log final status
            $message = "Campaign completed. Success: {$successCount}, Failures: {$failureCount}";
            $this->logCampaign($finalStatus === 'sent' ? 'success' : 'warning', $message);

            // Create notification
            $this->createNotification($finalStatus, $successCount, $failureCount);

        } catch (\Exception $e) {
            // Mark campaign as failed
            $this->campaign->update(['status' => 'failed']);
            $this->logCampaign('error', 'Campaign processing failed: ' . $e->getMessage());
            
            // Create failure notification
            $this->createNotification('failed', 0, 1);
        }
    }

    private function processRecipient(CampaignRecipient $recipient, SpunManagerService $service): void
    {
        $campaignData = [
            'name' => $this->campaign->name,
            'template_html' => $this->campaign->template_html,
            'scheduled_at' => $this->campaign->scheduled_at->toISOString(),
            'application_id' => $recipient->application_id,
            'list_id' => $recipient->list_id,
            'segment_id' => $recipient->segment_id,
        ];

        $result = $service->sendCampaign($campaignData);

        if ($result['success']) {
            $this->logRecipient($recipient, 'success', 'Campaign sent successfully', $result);
        } else {
            $this->logRecipient($recipient, 'error', 'Failed to send campaign: ' . ($result['error'] ?? 'Unknown error'), $result);
            throw new \Exception($result['error'] ?? 'Unknown error');
        }
    }

    private function logCampaign(string $type, string $message, array $extraData = []): void
    {
        CampaignLog::create([
            'campaign_id' => $this->campaign->id,
            'type' => $type,
            'message' => $message,
            'extra_data' => $extraData,
        ]);
    }

    private function logRecipient(CampaignRecipient $recipient, string $type, string $message, array $extraData = []): void
    {
        CampaignLog::create([
            'campaign_id' => $this->campaign->id,
            'campaign_recipient_id' => $recipient->id,
            'type' => $type,
            'message' => $message,
            'extra_data' => $extraData,
        ]);
    }

    private function createNotification(string $status, int $successCount, int $failureCount): void
    {
        $title = match($status) {
            'sent' => 'Campaign Sent Successfully',
            'failed' => 'Campaign Failed',
            default => 'Campaign Completed'
        };

        $message = match($status) {
            'sent' => "Campaign '{$this->campaign->name}' was sent successfully to {$successCount} recipients.",
            'failed' => "Campaign '{$this->campaign->name}' failed. {$failureCount} recipients could not be processed.",
            default => "Campaign '{$this->campaign->name}' completed with {$successCount} successes and {$failureCount} failures."
        };

        // Create notification for all users (in a real app, you might want to notify specific users)
        Notification::create([
            'user_id' => 1, // Default user for now
            'campaign_id' => $this->campaign->id,
            'title' => $title,
            'message' => $message,
        ]);
    }
}

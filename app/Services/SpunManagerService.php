<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpunManagerService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = 'http://host.docker.internal:80';
        $this->token = '1|psCvHlirHbgZ9WpVIVT0TSpUkJjUWHgJn0Ww8rGG40cc3ae7';
    }

    /**
     * Get all applications from SPUN-MANAGER
     */
    public function getApplications(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v1/site');

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::error('Failed to fetch applications from SPUN-MANAGER', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception while fetching applications', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Get lists for a specific application
     */
    public function getLists(string $applicationId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . "/api/v1/mailcoach/{$applicationId}/list");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::error('Failed to fetch lists from SPUN-MANAGER', [
                'application_id' => $applicationId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception while fetching lists', [
                'application_id' => $applicationId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Get segments for a specific list
     */
    public function getSegments(string $listId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . "/api/v1/mailcoach/{$listId}/segment");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? [];
            }

            Log::error('Failed to fetch segments from SPUN-MANAGER', [
                'list_id' => $listId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception while fetching segments', [
                'list_id' => $listId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [];
        }
    }

    /**
     * Send campaign to SPUN-MANAGER (fictitious endpoint)
     */
    public function sendCampaign(array $campaignData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . "/api/v1/mailcoach/{$campaignData['application_id']}/campaign", [
                'name' => $campaignData['name'],
                'template_html' => $campaignData['template_html'],
                'scheduled_at' => $campaignData['scheduled_at'],
                'list_id' => $campaignData['list_id'],
                'segment_id' => $campaignData['segment_id'],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status_code' => $response->status()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to send campaign',
                'status_code' => $response->status(),
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception while sending campaign', [
                'campaign_data' => $campaignData,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Get formatted options for applications
     */
    public function getApplicationOptions(): array
    {
        $applications = $this->getApplications();
        $options = [];

        foreach ($applications as $app) {
            $options[$app['uuid']] = $app['name'] ?? $app['uuid'];
        }

        return $options;
    }

    /**
     * Get formatted options for lists
     */
    public function getListOptions(string $applicationId): array
    {
        $lists = $this->getLists($applicationId);
        $options = [];

        foreach ($lists as $list) {
            $options[$list['id']] = $list['name'] ?? $list['id'];
        }

        return $options;
    }

    /**
     * Get formatted options for segments
     */
    public function getSegmentOptions(string $listId): array
    {
        $segments = $this->getSegments($listId);
        $options = [];

        foreach ($segments as $segment) {
            $options[$segment['id']] = $segment['name'] ?? $segment['id'];
        }

        return $options;
    }
}

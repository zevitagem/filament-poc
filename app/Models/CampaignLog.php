<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $campaign_id
 * @property int|null $campaign_recipient_id
 * @property string $type
 * @property string $message
 * @property array|null $extra_data
 */
class CampaignLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'campaign_recipient_id',
        'type',
        'message',
        'extra_data',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(CampaignRecipient::class, 'campaign_recipient_id');
    }

    public function scopeInfo($query)
    {
        return $query->where('type', 'info');
    }

    public function scopeWarning($query)
    {
        return $query->where('type', 'warning');
    }

    public function scopeError($query)
    {
        return $query->where('type', 'error');
    }

    public function scopeSuccess($query)
    {
        return $query->where('type', 'success');
    }
}

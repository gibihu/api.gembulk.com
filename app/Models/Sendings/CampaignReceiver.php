<?php

namespace App\Models\Sendings;

use App\Models\Sendings\Servers\Server;
use App\Models\Users\User;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CampaignReceiver extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_campaign_receivers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'receiver',
        'sender_name',
        'sent_status',
        'message',
        'response',
        'cost',
        'action_key',
        'sent_at',
        'campaign_id',
        'status',
        'response_report',
        'response_callback',
    ];
    protected $casts = [
        'is_scheduled' => 'boolean',
        'response' => 'json',
    ];
    protected $appends = [
        'status_text',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }
}

<?php

namespace App\Models\Sendings;

use App\Models\Sendings\Servers\Server;
use App\Models\Users\User;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_campaigns';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'user_id',
        'action_key',
        'receivers',
        'message',
        'data',
        'total_cost',
        'status',
        'sender_name',
        'sender_id',
        'user_id',
        'server_id',
        'server_name',
        'response',
        'response_callback',
        'response_report',
        'response_report_callback',
        'sent_at',
        'scheduled_at',
    ];

    protected $casts = [
        'data' => 'array',
        'receivers' => 'array',
        'response' => 'array',
        'response_callback' => 'array',
        'response_report' => 'array',
        'response_report_callback' => 'array',
    ];

    protected $appends = [
        'status_text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(Sender::class, 'sender_id', 'id');
    }

    public function receiver_s(): HasMany
    {
        return $this->hasMany(CampaignReceiver::class, 'campaign_id');
    }

    public function scopeHasPendingReport($q)
    {
        return $q->whereNotNull('response_report_callback->pending')
            ->where('response_report_callback->pending', '>', 1);
    }

}

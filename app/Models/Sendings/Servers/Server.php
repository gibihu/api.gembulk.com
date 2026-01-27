<?php

namespace App\Models\Sendings\Servers;

use App\Models\Sendings\Sender;
use App\Models\Users\User;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    use SoftDeletes;

    protected $table = 'sd_servers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'static_name',
        'host',
        'user_id',
        'status',
    ];

    protected $casts = [];

    protected $with = [];

    protected $hidden = [
//        'server_id',
//        'user_id',
    ];

    protected $appends = [
        'status_text',
    ];


    public function senders(): HasMany
    {
        return $this->hasMany(Sender::class, 'server_id');
    }
    public function actions(): HasMany
    {
        return $this->hasMany(ServerAction::class, 'server_id');
    }
    public function getActionSmsAttribute()
    {
        return $this->actions()
            ->where('action_key', 'sms')
            ->first();
    }
    public function getActionReportSmsAttribute()
    {
        return $this->actions()
            ->where('action_key', 'sms_report')
            ->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

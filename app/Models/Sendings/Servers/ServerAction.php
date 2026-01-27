<?php

namespace App\Models\Sendings\Servers;

use App\Models\Sendings\Sender;
use App\Models\Users\User;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServerAction extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_server_actions';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'server_id',
        'action_key',
        'method',
        'endpoint',
        'headers',
        'body',
        'response',
        'settings',
        'status',
    ];

    protected $casts = [
        'headers' => 'array',
        'body' => 'array',
        'response' => 'array',
        'settings' => 'array',
    ];

    protected $with = [];

    protected $hidden = [
//        'server_id',
//        'user_id',
    ];

    protected $appends = [
        'status_text',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

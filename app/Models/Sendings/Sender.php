<?php

namespace App\Models\Sendings;

use App\Models\Sendings\Servers\Server;
use App\Models\Users\User;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_senders';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'user_id',
        'server_id',
        'status',
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
}

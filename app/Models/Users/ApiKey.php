<?php

namespace App\Models\Users;

use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_api_keys';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'token',
        'template',
        'options',
        'permissions',
        'user_id',
        'status',
    ];

    protected $appends = [
        'status_text',
    ];

    protected $casts = [
        'options' => 'array',
        'permissions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function generateKey(): string
    {
        do {
            // สุ่ม 32 bytes = 64 ตัวอักษร hex
            $token = bin2hex(random_bytes(32));
        } while (self::where('token', $token)->exists());

        return $token;
    }
}

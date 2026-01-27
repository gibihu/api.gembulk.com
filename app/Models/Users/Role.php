<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUuids;
    protected $table = 'user_roles';
    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'name',
        'full_name',
        'orders',
    ];

    protected $hidden = [
        'orders',
    ];
}

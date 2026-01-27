<?php

namespace App\Models\Sendings;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SpamWord extends Model
{
    use HasUuids;
    protected $table = 'sd_spam_words';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'word'
    ];
}

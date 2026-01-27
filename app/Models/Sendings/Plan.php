<?php

namespace App\Models\Sendings;

use App\Models\Sendings\Servers\Server;
use App\Traits\GlobalStatusTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use GlobalStatusTrait;
    use HasUuids;
    protected $table = 'sd_plans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'details',
        'price',
        'currency',
        'credit_limit',
        'orders',
        'options',
        'custom_plans',
        'duration',
        'duration_unit',
        'servers',
        'status',
        'public',
        'more_than',
        'recommended',
        'tax_rate',
        'tax_type',
        'fee_label',
        'fee_rate',
        'fee_type',
    ];

    protected $casts = [
        'options' => 'array',
        'servers' => 'array',
        'custom_plans' => 'array',
        'public' => 'boolean',
        'price' => 'float',
        'more_than' => 'float',
        'recommended' => 'boolean',
        'tax_rate' => 'float',
        'fee_rate' => 'float',
    ];

    protected $appends = [
        'status_text',
        'duration_unit_text',
        'duration_unit_th_text',
    ];

//    แปลงเป็นข้อความ
    public function serversRelation()
    {
        return $this->hasMany(Server::class, 'id', 'servers');
    }
//    แปลงกลับตอนบันทึก
    public function setServersAttribute($value)
    {
        $this->attributes['servers'] = json_encode($value);
    }


    const DURATION_UNIT_DAYS = 0;
    const DURATION_UNIT_MONTHS = 1;
    const DURATION_UNIT_YEARS = 2;
    const DURATION_UNIT_LIFETIME = 3;

    public static array $durationUnitLabels = [
        self::DURATION_UNIT_DAYS => 'days',
        self::DURATION_UNIT_MONTHS => 'months',
        self::DURATION_UNIT_YEARS => 'years',
        self::DURATION_UNIT_LIFETIME => 'lifetime',
    ];

    public static array $durationUnitThLabels = [
        self::DURATION_UNIT_DAYS => 'วัน',
        self::DURATION_UNIT_MONTHS => 'เดือน',
        self::DURATION_UNIT_YEARS => 'ปี',
        self::DURATION_UNIT_LIFETIME => 'ตลอดชีพ',
    ];

// EN
    public function getDurationUnitTextAttribute(): string
    {
        return self::$durationUnitLabels[$this->duration_unit] ?? 'unknown';
    }

// TH 🇹🇭
    public function getDurationUnitThTextAttribute(): string
    {
        return self::$durationUnitThLabels[$this->duration_unit] ?? 'ไม่ทราบ';
    }



}

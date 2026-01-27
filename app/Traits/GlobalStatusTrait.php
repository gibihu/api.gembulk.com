<?php

namespace App\Traits;

trait GlobalStatusTrait
{
    // ─────────────────────────
    // Lifecycle / Process
    // ─────────────────────────
    const STATUS_DRAFT        = 0;  // ร่าง / ยังไม่พร้อม
    const STATUS_PENDING      = 1;  // รอการดำเนินการ / รออนุมัติ
    const STATUS_PROCESSING   = 2;  // กำลังดำเนินการ
    const STATUS_COMPLETED    = 3;  // เสร็จสมบูรณ์
    const STATUS_FAILED       = 4;  // ล้มเหลว (system)
    const STATUS_REJECTED     = 5;  // ถูกปฏิเสธ (manual / policy)
    const STATUS_CANCELLED    = 6;  // ถูกยกเลิก

    // ─────────────────────────
    // Usage / Availability
    // ─────────────────────────
    const STATUS_ACTIVE       = 10; // ใช้งานอยู่
    const STATUS_INACTIVE     = 11; // ปิดชั่วคราว
    const STATUS_SUSPENDED    = 12; // ถูกระงับ

    // ─────────────────────────
    // Moderation / Control
    // ─────────────────────────
    const STATUS_FLAGGED      = 20; // ถูกแจ้ง
    const STATUS_RESTRICTED  = 21; // ถูกจำกัดสิทธิ์
    const STATUS_UNDER_REVIEW = 22; // อยู่ระหว่างตรวจสอบ

    // ─────────────────────────
    // End of life
    // ─────────────────────────
    const STATUS_ARCHIVED     = 30; // เก็บถาวร
    const STATUS_DELETED      = 31; // ลบแล้ว (soft delete)

    // ─────────────────────────
    // Distribution / Publishing
    // ─────────────────────────
    const STATUS_PUBLISHED    = 40; // เผยแผร่แล้ว
    const STATUS_PRIVATE  = 41; // ยังไม่เผยแผร่
    const STATUS_SCHEDULED    = 42; // กำหนดเผยแผร่

    // | ช่วง  | หมวด           |
    // | ----- | -------------- |
    // | 0–9   | lifecycle      |
    // | 10–19 | usage          |
    // | 20–29 | moderation     |
    // | 30–39 | end of life    |
    // | 40–49 | distribution   |

    // ─────────────────────────
    // Labels (ใช้กับ UI / API)
    // ─────────────────────────
    public static array $statusLabels = [
        self::STATUS_DRAFT        => 'draft',
        self::STATUS_PENDING      => 'pending',
        self::STATUS_PROCESSING   => 'processing',
        self::STATUS_COMPLETED    => 'completed',
        self::STATUS_FAILED       => 'failed',
        self::STATUS_REJECTED     => 'rejected',
        self::STATUS_CANCELLED    => 'cancelled',

        self::STATUS_ACTIVE       => 'active',
        self::STATUS_INACTIVE     => 'inactive',
        self::STATUS_SUSPENDED    => 'suspended',

        self::STATUS_FLAGGED      => 'flagged',
        self::STATUS_RESTRICTED  => 'restricted',
        self::STATUS_UNDER_REVIEW => 'under_review',

        self::STATUS_ARCHIVED     => 'archived',
        self::STATUS_DELETED      => 'deleted',

        self::STATUS_PUBLISHED    => 'published',
        self::STATUS_PRIVATE      => 'private',
        self::STATUS_SCHEDULED    => 'scheduled',
    ];

    // ─────────────────────────
    // Reverse map (string → int)
    // ─────────────────────────
    public static array $statusMap = [
        'draft'        => self::STATUS_DRAFT,
        'pending'      => self::STATUS_PENDING,
        'processing'   => self::STATUS_PROCESSING,
        'completed'    => self::STATUS_COMPLETED,
        'failed'       => self::STATUS_FAILED,
        'rejected'     => self::STATUS_REJECTED,
        'cancelled'    => self::STATUS_CANCELLED,

        'active'       => self::STATUS_ACTIVE,
        'inactive'     => self::STATUS_INACTIVE,
        'suspended'    => self::STATUS_SUSPENDED,

        'flagged'      => self::STATUS_FLAGGED,
        'restricted'  => self::STATUS_RESTRICTED,
        'under_review'=> self::STATUS_UNDER_REVIEW,

        'archived'     => self::STATUS_ARCHIVED,
        'deleted'      => self::STATUS_DELETED,

        'published'    => self::STATUS_PUBLISHED,
        'private'      => self::STATUS_PRIVATE,
        'scheduled'    => self::STATUS_SCHEDULED,
    ];

    // ─────────────────────────
    // Accessor
    // ─────────────────────────
    public function getStatusTextAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? 'unknown';
    }

    // ─────────────────────────
    // Helpers
    // ─────────────────────────
    public function isFinalStatus(): bool
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
            self::STATUS_ARCHIVED,
            self::STATUS_DELETED,
        ], true);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUSPENDED,
            self::STATUS_RESTRICTED,
            self::STATUS_REJECTED,
        ], true);
    }

    // ─────────────────────────
    // Convert string → status
    // ─────────────────────────
    public static function fromString(string $status): ?int
    {
        return self::$statusMap[strtolower($status)] ?? null;
    }
}

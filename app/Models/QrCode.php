<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'reservation_id', 'token', 'qr_image_path', 'is_used',
        'scanned_by', 'scanned_at', 'expires_at',
    ];

    protected $casts = [
        'is_used'    => 'boolean',
        'scanned_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function scannedBy()   { return $this->belongsTo(User::class, 'scanned_by'); }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}

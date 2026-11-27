<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingDelivery extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'file_reference',
        'notes',
        'source_type',
        'original_name',
        'mime_type',
        'size_bytes',
        'delivery_type',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
            'size_bytes' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function deliveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by_user_id');
    }

    public function previousDelivery(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function bookingItem(): BelongsTo
    {
        return $this->belongsTo(BookingItem::class);
    }

    public function nextDeliveries(): HasMany
    {
        return $this->hasMany(self::class, 'previous_delivery_id');
    }
}

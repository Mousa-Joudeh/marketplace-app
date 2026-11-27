<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingMessage extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'body',
        'attachment_storage_key',
        'attachment_original_name',
        'attachment_mime_type',
        'attachment_size_bytes',
        'client_message_id',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'attachment_size_bytes' => 'integer',
            'edited_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_to_message_id');
    }
}

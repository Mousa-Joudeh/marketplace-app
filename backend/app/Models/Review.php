<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rating',
        'comment',
        'title',
        'quality_rating',
        'communication_rating',
        'value_rating',
        'is_anonymous',
        'review_media',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'quality_rating' => 'integer',
            'communication_rating' => 'integer',
            'value_rating' => 'integer',
            'provider_replied_at' => 'datetime',
            'moderated_at' => 'datetime',
            'is_anonymous' => 'boolean',
            'review_media' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by_user_id');
    }
}

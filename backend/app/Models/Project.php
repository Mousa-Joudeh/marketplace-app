<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'brief',
        'budget',
        'brand_name',
        'objective',
        'target_audience',
        'target_channels',
        'planned_deliverables',
        'target_delivery_date',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:3',
            'target_channels' => 'array',
            'planned_deliverables' => 'array',
            'target_delivery_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ProjectAsset::class);
    }

    public function aiSessions(): HasMany
    {
        return $this->hasMany(AiSession::class);
    }
}

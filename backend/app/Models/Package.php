<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'deliverables',
        'price',
        'slug',
        'description',
        'scope_limits',
        'client_requirements',
        'session_duration_minutes',
        'max_participants',
        'booking_lead_time_hours',
        'delivery_turnaround_days',
        'revision_limit',
        'bookable_from',
        'bookable_until',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'session_duration_minutes' => 'integer',
            'max_participants' => 'integer',
            'booking_lead_time_hours' => 'integer',
            'delivery_turnaround_days' => 'integer',
            'revision_limit' => 'integer',
            'bookable_from' => 'datetime',
            'bookable_until' => 'datetime',
        ];
    }

    public function providerProfile(): BelongsTo
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function catalogImages(): HasMany
    {
        return $this->hasMany(CatalogImage::class);
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(ProviderPortfolioItem::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function favoriteItems(): HasMany
    {
        return $this->hasMany(FavoriteItem::class);
    }

    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}

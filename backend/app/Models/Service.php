<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'service_kind',
        'location_mode',
        'pricing_unit',
        'price',
        'is_addon_only',
        'slug',
        'description',
        'deliverables',
        'scope_limits',
        'client_requirements',
        'estimated_duration_minutes',
        'min_booking_minutes',
        'max_booking_minutes',
        'booking_lead_time_hours',
        'delivery_turnaround_days',
        'revision_limit',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'is_addon_only' => 'boolean',
            'estimated_duration_minutes' => 'integer',
            'min_booking_minutes' => 'integer',
            'max_booking_minutes' => 'integer',
            'booking_lead_time_hours' => 'integer',
            'delivery_turnaround_days' => 'integer',
            'revision_limit' => 'integer',
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

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    public function serviceEquipment(): HasMany
    {
        return $this->hasMany(ServiceEquipment::class);
    }

    public function packageItems(): HasMany
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'hourly_rate',
        'address',
        'city',
        'district',
        'latitude',
        'longitude',
        'capacity',
        'area_sqm',
        'amenities',
        'house_rules',
        'min_booking_minutes',
        'max_booking_minutes',
        'booking_buffer_minutes',
        'check_in_instructions',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:3',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'capacity' => 'integer',
            'area_sqm' => 'decimal:2',
            'amenities' => 'array',
            'min_booking_minutes' => 'integer',
            'max_booking_minutes' => 'integer',
            'booking_buffer_minutes' => 'integer',
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

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function spaceEquipment(): HasMany
    {
        return $this->hasMany(SpaceEquipment::class);
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

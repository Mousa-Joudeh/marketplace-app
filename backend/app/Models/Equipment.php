<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'title',
        'stock_quantity',
        'addon_price',
        'brand',
        'model',
        'description',
        'specifications',
        'usage_notes',
        'inventory_code',
        'out_of_service_quantity',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'addon_price' => 'decimal:3',
            'specifications' => 'array',
            'out_of_service_quantity' => 'integer',
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

    public function spaceEquipment(): HasMany
    {
        return $this->hasMany(SpaceEquipment::class);
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

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}

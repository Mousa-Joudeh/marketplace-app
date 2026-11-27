<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceEquipment extends Model
{
    protected $table = 'service_equipment';

    protected $fillable = [
        'quantity',
        'is_optional',
        'addon_price',
        'notes',
        'sort_order',
        'addon_pricing_unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_optional' => 'boolean',
            'addon_price' => 'decimal:3',
            'sort_order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}

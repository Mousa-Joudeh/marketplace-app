<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderProfile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'display_name',
        'slug',
        'city',
        'tagline',
        'bio',
        'district',
        'public_address',
        'latitude',
        'longitude',
        'logo_path',
        'cover_path',
        'instagram_url',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

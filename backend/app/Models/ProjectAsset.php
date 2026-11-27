<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAsset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'original_name',
        'storage_key',
        'display_name',
        'asset_type',
        'description',
        'mime_type',
        'size_bytes',
        'preview_storage_key',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'shared_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sharedBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

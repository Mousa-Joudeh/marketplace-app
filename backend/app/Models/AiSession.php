<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiSession extends Model
{
    protected $fillable = [
        'inputs',
        'tool_key',
        'title',
    ];

    protected function casts(): array
    {
        return [
            'inputs' => 'array',
            'output' => 'array',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sourceSession(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_ai_session_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AiSubscription::class, 'ai_subscription_id');
    }

    public function derivedSessions(): HasMany
    {
        return $this->hasMany(self::class, 'source_ai_session_id');
    }
}

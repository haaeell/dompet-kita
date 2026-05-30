<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerLocation extends Model
{
    protected $fillable = [
        'couple_id',
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'label',
        'is_active',
        'last_seen_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

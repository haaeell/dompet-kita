<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'couple_id',
        'user_id',
        'name',
        'type',
        'purchase_value',
        'current_value',
        'acquired_at',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'purchase_value' => 'float',
        'current_value' => 'float',
        'acquired_at' => 'date',
        'is_active' => 'boolean',
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

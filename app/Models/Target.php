<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Target extends Model
{
    protected $fillable = [
        'couple_id',
        'name',
        'icon',
        'target_amount',
        'current_amount',
        'deadline',
        'status',
        'color'
    ];

    protected $casts = ['deadline' => 'date', 'target_amount' => 'float', 'current_amount' => 'float'];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(TargetSaving::class);
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }
}

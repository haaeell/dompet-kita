<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TargetSaving extends Model
{
    protected $fillable = ['target_id', 'user_id', 'amount', 'notes', 'date'];

    protected $casts = ['date' => 'date', 'amount' => 'float'];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::created(fn($s) => $s->target->increment('current_amount', $s->amount));
        static::deleted(fn($s) => $s->target->decrement('current_amount', $s->amount));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TRANSFER_CATEGORY = 'Transfer Antar Bank';

    protected $fillable = [
        'couple_id',
        'user_id',
        'category_id',
        'bank_id',
        'type',
        'amount',
        'description',
        'notes',
        'date',
        'receipt_image',
        'client_uuid'
    ];

    protected $casts = ['date' => 'date', 'amount' => 'float'];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function scopeNonTransfer($query)
    {
        return $query->whereDoesntHave('category', function ($categoryQuery) {
            $categoryQuery->where('name', self::TRANSFER_CATEGORY);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($tx) {
            $tx->bank->recalculateBalance();
        });

        static::updated(function ($tx) {
            $tx->bank->recalculateBalance();
        });

        static::deleted(function ($tx) {
            $tx->bank->recalculateBalance();
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    protected $fillable = [
        'couple_id',
        'user_id',
        'type',
        'amount',
        'counterparty',
        'purpose',
        'due_date',
        'bank_id',
        'settlement_bank_id',
        'status',
        'paid_at',
        'notes',
        'initial_transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'due_date' => 'date',
        'paid_at' => 'date',
    ];

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function settlementBank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'settlement_bank_id');
    }

    public function initialTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'initial_transaction_id');
    }
}

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
        'installment_count',
        'installment_amount',
        'paid_amount',
        'counterparty',
        'purpose',
        'due_date',
        'bank_id',
        'settlement_bank_id',
        'status',
        'paid_at',
        'last_payment_at',
        'notes',
        'initial_transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'installment_amount' => 'float',
        'paid_amount' => 'float',
        'due_date' => 'date',
        'paid_at' => 'date',
        'last_payment_at' => 'date',
    ];

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function getInstallmentProgressAttribute(): string
    {
        $installmentAmount = (float) ($this->installment_amount ?: $this->amount);
        $paidInstallments = $installmentAmount > 0
            ? min((int) $this->installment_count, (int) floor(((float) $this->paid_amount + 0.01) / $installmentAmount))
            : 0;

        return $paidInstallments . '/' . max(1, (int) $this->installment_count);
    }

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

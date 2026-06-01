<?php

namespace App\Models;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Couple extends Model
{
    protected $fillable = [
        'couple_name',
        'invite_code',
        'avatar_couple',
        'currency'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($couple) {
            $couple->invite_code = strtoupper(Str::random(8));
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(Target::class);
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function billReminders(): HasMany
    {
        return $this->hasMany(BillReminder::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function getTotalIncomeAttribute(): float
    {
        return $this->transactions()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryBudget extends Model
{
    protected $fillable = [
        'couple_id',
        'category_id',
        'budget_month',
        'amount',
    ];

    protected $casts = [
        'budget_month' => 'date',
        'amount' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function couple(): BelongsTo
    {
        return $this->belongsTo(Couple::class);
    }
}

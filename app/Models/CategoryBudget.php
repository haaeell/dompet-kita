<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
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

    public static function effectiveForMonth(int $coupleId, CarbonInterface $month): Collection
    {
        return static::where('couple_id', $coupleId)
            ->whereDate('budget_month', '<=', $month->copy()->startOfMonth()->toDateString())
            ->latest('budget_month')
            ->latest('id')
            ->get()
            ->unique('category_id')
            ->mapWithKeys(fn (self $budget) => [(int) $budget->category_id => $budget]);
    }

    public static function activeForMonth(int $coupleId, CarbonInterface $month): Collection
    {
        return static::effectiveForMonth($coupleId, $month)
            ->filter(fn (self $budget) => (float) $budget->amount > 0);
    }
}

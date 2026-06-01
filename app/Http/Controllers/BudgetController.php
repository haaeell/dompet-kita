<?php

namespace App\Http\Controllers;

use App\Models\CategoryBudget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $couple = Auth::user()->couple;
        $budgetMonth = $this->resolveBudgetMonth($request->query('month'));
        $categories = $couple->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $currentMonthBudgets = CategoryBudget::where('couple_id', $couple->id)
            ->whereDate('budget_month', $budgetMonth->toDateString())
            ->get()
            ->mapWithKeys(fn (CategoryBudget $budget) => [(int) $budget->category_id => $budget]);

        $effectiveBudgets = CategoryBudget::effectiveForMonth($couple->id, $budgetMonth);
        $activeBudgets = $effectiveBudgets->filter(fn (CategoryBudget $budget) => (float) $budget->amount > 0);

        $spentByCategory = $couple->transactions()
            ->nonTransfer()
            ->where('type', 'expense')
            ->whereBetween('date', [$budgetMonth->copy()->startOfMonth(), $budgetMonth->copy()->endOfMonth()])
            ->select('category_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_id')
            ->pluck('total_amount', 'category_id');

        $budgetedCategories = $categories
            ->filter(fn($category) => $activeBudgets->has($category->id))
            ->values();

        $savedBudgetMonths = CategoryBudget::where('couple_id', $couple->id)
            ->select('budget_month')
            ->distinct()
            ->orderByDesc('budget_month')
            ->pluck('budget_month')
            ->map(fn($month) => Carbon::parse($month)->startOfMonth());

        $budgetMonths = collect(range(0, 11))
            ->map(fn (int $offset) => $budgetMonth->copy()->subMonths($offset)->startOfMonth())
            ->merge($savedBudgetMonths)
            ->unique(fn (Carbon $month) => $month->format('Y-m'))
            ->sortByDesc(fn (Carbon $month) => $month->format('Y-m'))
            ->values();

        $historyMonths = $budgetMonths->take(12)->map(function (Carbon $month) use ($couple) {
            $monthBudgets = CategoryBudget::activeForMonth($couple->id, $month);
            $budgetedCategoryIds = $monthBudgets->keys()->all();

            $spent = empty($budgetedCategoryIds)
                ? 0
                : $couple->transactions()
                    ->nonTransfer()
                    ->where('type', 'expense')
                    ->whereIn('category_id', $budgetedCategoryIds)
                    ->whereBetween('date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->sum('amount');

            $total = $monthBudgets->sum('amount');

            return [
                'month' => $month,
                'budget_total' => $total,
                'spent_total' => $spent,
                'percent' => $total > 0 ? min(100, round(($spent / $total) * 100)) : 0,
            ];
        });

        return view('budgets.index', compact(
            'categories',
            'budgetedCategories',
            'activeBudgets',
            'currentMonthBudgets',
            'spentByCategory',
            'budgetMonth',
            'budgetMonths',
            'historyMonths'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'budget_month' => ['nullable', 'date_format:Y-m'],
        ]);

        $couple = Auth::user()->couple;
        $category = $couple->categories()
            ->where('type', 'expense')
            ->findOrFail($data['category_id']);
        $amount = $request->boolean('clear_budget')
            ? 0
            : (float) ($data['amount'] ?? 0);
        $budgetMonth = $this->resolveBudgetMonth($data['budget_month'] ?? null);

        if ($amount <= 0) {
            CategoryBudget::updateOrCreate([
                'couple_id' => $couple->id,
                'category_id' => $category->id,
                'budget_month' => $budgetMonth->toDateString(),
            ], [
                'amount' => 0,
            ]);

            return redirect()
                ->route('budgets.index', ['month' => $budgetMonth->format('Y-m')])
                ->with('success', 'Budget kategori dinonaktifkan mulai bulan ini.');
        }

        CategoryBudget::updateOrCreate([
            'couple_id' => $couple->id,
            'category_id' => $category->id,
            'budget_month' => $budgetMonth->toDateString(),
        ], [
            'amount' => $amount,
        ]);

        return redirect()
            ->route('budgets.index', ['month' => $budgetMonth->format('Y-m')])
            ->with('success', 'Budget kategori berhasil disimpan.');
    }

    private function resolveBudgetMonth(?string $month): Carbon
    {
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        return now()->startOfMonth();
    }
}

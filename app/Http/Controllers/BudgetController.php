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
            ->keyBy('category_id');

        $fallbackBudgets = CategoryBudget::where('couple_id', $couple->id)
            ->whereDate('budget_month', '<', $budgetMonth->toDateString())
            ->latest('budget_month')
            ->latest('id')
            ->get()
            ->unique('category_id')
            ->keyBy('category_id');

        $activeBudgets = $fallbackBudgets->merge($currentMonthBudgets);

        $spentByCategory = $couple->transactions()
            ->nonTransfer()
            ->where('type', 'expense')
            ->whereMonth('date', $budgetMonth->month)
            ->whereYear('date', $budgetMonth->year)
            ->select('category_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category_id')
            ->pluck('total_amount', 'category_id');

        $budgetedCategories = $categories
            ->filter(fn($category) => $activeBudgets->has($category->id))
            ->values();

        $budgetMonths = CategoryBudget::where('couple_id', $couple->id)
            ->select('budget_month')
            ->distinct()
            ->orderByDesc('budget_month')
            ->pluck('budget_month')
            ->map(fn($month) => Carbon::parse($month)->startOfMonth());

        if (! $budgetMonths->contains(fn($month) => $month->isSameMonth($budgetMonth))) {
            $budgetMonths->prepend($budgetMonth->copy());
        }

        $historyMonths = $budgetMonths->take(12)->map(function (Carbon $month) use ($couple) {
            $monthBudgets = CategoryBudget::where('couple_id', $couple->id)
                ->whereDate('budget_month', $month->toDateString())
                ->get();

            $spent = $couple->transactions()
                ->nonTransfer()
                ->where('type', 'expense')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
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
            CategoryBudget::where('couple_id', $couple->id)
                ->where('category_id', $category->id)
                ->whereDate('budget_month', $budgetMonth->toDateString())
                ->delete();

            return redirect()
                ->route('budgets.index', ['month' => $budgetMonth->format('Y-m')])
                ->with('success', 'Budget kategori dihapus untuk bulan ini.');
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

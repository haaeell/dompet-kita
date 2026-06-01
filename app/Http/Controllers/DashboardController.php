<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Bank;
use App\Models\Target;
use App\Models\TargetSaving;
use App\Models\CategoryBudget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $couple = Auth::user()->couple;
        $month = now()->month;
        $year = now()->year;

        $coupleMembers = $couple->users;

        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? $couple->users()->find($selectedUserId) : null;

        $transactionQuery = $couple->transactions();
        $incomeQuery = $couple->transactions()->nonTransfer()->where('type', 'income');
        $expenseQuery = $couple->transactions()->nonTransfer()->where('type', 'expense');

        if ($selectedUser) {
            $transactionQuery->where('user_id', $selectedUserId);
            $incomeQuery->where('user_id', $selectedUserId);
            $expenseQuery->where('user_id', $selectedUserId);
        }

        $transactions = $transactionQuery
            ->with(['user', 'category', 'bank'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->latest('date')
            ->latest('id')
            ->take(10)
            ->get();

        $monthlyIncome = $incomeQuery
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->sum('amount');

        $monthlyExpense = $expenseQuery
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->sum('amount');

        $banksQuery = $couple->banks()->where('is_active', true);
        if ($selectedUser) {
            $banksQuery->where('account_name', $selectedUser->name);
        }

        $banks = $banksQuery->get();
        $totalWealth = $banks->sum('current_balance');
        $debtQuery = $couple->debts();
        if ($selectedUser) {
            $debtQuery->where('user_id', $selectedUserId);
        }

        $outstandingHutang = (clone $debtQuery)->where('type', 'hutang')->where('status', 'pending')->sum('amount');
        $outstandingPiutang = (clone $debtQuery)->where('type', 'piutang')->where('status', 'pending')->sum('amount');
        $totalWealthIncludingPiutang = $totalWealth + $outstandingPiutang;
        $targets = $couple->targets()->where('status', 'active')->latest()->take(3)->get();

        $debtReminderQuery = $couple->debts()
            ->with('user')
            ->where('status', 'pending')
            ->whereDate('due_date', '<=', now()->addDays(3)->toDateString());

        if ($selectedUser) {
            $debtReminderQuery->where('user_id', $selectedUserId);
        }

        $debtReminders = $debtReminderQuery
            ->orderBy('due_date')
            ->take(6)
            ->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $dayIncome = $couple->transactions()->nonTransfer()->where('type', 'income')->whereDate('date', $date->toDateString());
            $dayExpense = $couple->transactions()->nonTransfer()->where('type', 'expense')->whereDate('date', $date->toDateString());

            if ($selectedUserId) {
                $dayIncome->where('user_id', $selectedUserId);
                $dayExpense->where('user_id', $selectedUserId);
            }

            $chartData[] = [
                'date' => $date->format('d M'),
                'income' => $dayIncome->sum('amount'),
                'expense' => $dayExpense->sum('amount'),
            ];
        }

        $expenseByCategoryQuery = $couple->transactions()
            ->nonTransfer()
            ->where('type', 'expense')
            ->whereMonth('date', $month)->whereYear('date', $year);

        if ($selectedUserId) {
            $expenseByCategoryQuery->where('user_id', $selectedUserId);
        }

        $expenseByCategory = $expenseByCategoryQuery
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(fn($group) => [
                'name' => $group->first()->category->name,
                'icon' => $group->first()->category->icon,
                'color' => $group->first()->category->color,
                'amount' => $group->sum('amount'),
            ])->values()->sortByDesc('amount')->take(5);

        $achievementBadges = collect();

        if ($couple->transactions()->exists()) {
            $achievementBadges->push([
                'icon' => 'fa-receipt',
                'title' => 'Mulai Mencatat',
                'description' => 'Transaksi pertama sudah tercatat.',
                'color' => '#db2777',
            ]);
        }

        if ($coupleMembers->count() >= 2) {
            $achievementBadges->push([
                'icon' => 'fa-user-group',
                'title' => 'Pasangan Kompak',
                'description' => 'Dua akun sudah terhubung dalam satu dompet.',
                'color' => '#7c3aed',
            ]);
        }

        if ($monthlyIncome > 0 && $monthlyIncome > $monthlyExpense) {
            $achievementBadges->push([
                'icon' => 'fa-piggy-bank',
                'title' => 'Bulan Surplus',
                'description' => 'Pemasukan bulan ini masih lebih besar dari pengeluaran.',
                'color' => '#16a34a',
            ]);
        }

        $currentBudgetMonth = now()->startOfMonth();
        $budgetRows = CategoryBudget::where('couple_id', $couple->id)
            ->latest('budget_month')
            ->latest('id')
            ->get();
        $budgetRows = $budgetRows->unique('category_id')->values();

        if ($budgetRows->isNotEmpty()) {
            $spentForBudget = $couple->transactions()
                ->nonTransfer()
                ->where('type', 'expense')
                ->whereMonth('date', $currentBudgetMonth->month)
                ->whereYear('date', $currentBudgetMonth->year)
                ->select('category_id', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('category_id')
                ->pluck('total_amount', 'category_id');

            $safeBudgetCount = $budgetRows->filter(function ($budget) use ($spentForBudget) {
                return (float) ($spentForBudget[$budget->category_id] ?? 0) <= (float) $budget->amount;
            })->count();

            $achievementBadges->push([
                'icon' => $safeBudgetCount === $budgetRows->count() ? 'fa-shield-heart' : 'fa-chart-pie',
                'title' => $safeBudgetCount === $budgetRows->count() ? 'Budget Aman' : 'Budget Aktif',
                'description' => $safeBudgetCount === $budgetRows->count()
                    ? 'Semua kategori berbudget masih dalam batas bulan ini.'
                    : "{$safeBudgetCount} dari {$budgetRows->count()} budget kategori masih aman.",
                'color' => $safeBudgetCount === $budgetRows->count() ? '#0891b2' : '#f59e0b',
            ]);
        }

        if ($couple->targets()->where('status', 'completed')->exists()) {
            $achievementBadges->push([
                'icon' => 'fa-trophy',
                'title' => 'Target Tercapai',
                'description' => 'Ada target tabungan yang berhasil diselesaikan.',
                'color' => '#f59e0b',
            ]);
        }

        return view('dashboard.index', compact(
            'couple',
            'coupleMembers',
            'selectedUserId',
            'transactions',
            'monthlyIncome',
            'monthlyExpense',
            'banks',
            'totalWealth',
            'totalWealthIncludingPiutang',
            'outstandingHutang',
            'outstandingPiutang',
            'targets',
            'debtReminders',
            'chartData',
            'expenseByCategory',
            'achievementBadges'
        ));
    }
}

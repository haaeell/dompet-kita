<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Bank;
use App\Models\Target;
use App\Models\TargetSaving;
use App\Models\CategoryBudget;
use App\Models\BillReminder;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $couple = Auth::user()->couple;
        $month = now()->month;
        $year = now()->year;

        $coupleMembers = $couple->users;

        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? $couple->users()->find($selectedUserId) : null;

        $transactionQuery = $couple->transactions()->visibleTo(Auth::user());
        $incomeQuery = $couple->transactions()->visibleTo(Auth::user())->nonTransfer()->where('type', 'income');
        $expenseQuery = $couple->transactions()->visibleTo(Auth::user())->nonTransfer()->where('type', 'expense');

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
            $banksQuery->where('user_id', $selectedUser->id);
        }

        $banks = $banksQuery->get();
        $totalWealth = $banks->sum('current_balance');
        $assetsQuery = $couple->assets()->where('is_active', true);
        if ($selectedUser) {
            $assetsQuery->where('user_id', $selectedUser->id);
        }
        $totalAssets = (float) $assetsQuery->sum('current_value');
        $debtQuery = $couple->debts();
        if ($selectedUser) {
            $debtQuery->where('user_id', $selectedUserId);
        }

        $outstandingHutang = (clone $debtQuery)->where('type', 'hutang')->where('status', 'pending')->sum(DB::raw('amount - paid_amount'));
        $outstandingPiutang = (clone $debtQuery)->where('type', 'piutang')->where('status', 'pending')->sum(DB::raw('amount - paid_amount'));
        $totalWealthIncludingPiutang = $totalWealth + $outstandingPiutang;
        $netWorth = $totalWealth + $totalAssets + $outstandingPiutang - $outstandingHutang;
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

        $billReminders = $couple->billReminders()
            ->with(['user', 'bank', 'category'])
            ->where('is_paid', false)
            ->whereDate('due_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('due_date')
            ->take(6)
            ->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $dayIncome = $couple->transactions()->visibleTo(Auth::user())->nonTransfer()->where('type', 'income')->whereDate('date', $date->toDateString());
            $dayExpense = $couple->transactions()->visibleTo(Auth::user())->nonTransfer()->where('type', 'expense')->whereDate('date', $date->toDateString());

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
            ->visibleTo(Auth::user())
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
        $budgetRows = CategoryBudget::activeForMonth($couple->id, $currentBudgetMonth)->values();
        $budgetNotifications = collect();

        if ($budgetRows->isNotEmpty()) {
            $safeBudgetCount = $budgetRows->filter(function ($budget) use ($couple, $currentBudgetMonth, $budgetNotifications) {
                $spent = $couple->transactions()
                    ->visibleTo(Auth::user())
                    ->nonTransfer()
                    ->where('type', 'expense')
                    ->where('category_id', $budget->category_id)
                    ->when($budget->user_id, fn ($query) => $query->where('user_id', $budget->user_id))
                    ->when($budget->bank_id, fn ($query) => $query->where('bank_id', $budget->bank_id))
                    ->whereBetween('date', [$currentBudgetMonth->copy()->startOfMonth(), $currentBudgetMonth->copy()->endOfMonth()])
                    ->sum('amount');
                $ratio = $budget->amount > 0 ? $spent / $budget->amount : 0;

                if ($ratio >= .8) {
                    $budgetNotifications->push([
                        'title' => $budget->category?->name ?? 'Budget',
                        'spent' => $spent,
                        'amount' => $budget->amount,
                        'percent' => min(999, round($ratio * 100)),
                        'status' => $ratio >= 1 ? 'Lewat batas' : 'Hampir habis',
                    ]);
                }

                return (float) $spent <= (float) $budget->amount;
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

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyTransactions = $couple->transactions()
            ->visibleTo(Auth::user())
            ->nonTransfer()
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->with(['category', 'user'])
            ->get();
        $weeklyExpense = $weeklyTransactions->where('type', 'expense')->sum('amount');
        $weeklyIncome = $weeklyTransactions->where('type', 'income')->sum('amount');
        $weeklyTopCategory = $weeklyTransactions->where('type', 'expense')->groupBy('category_id')
            ->map(fn ($rows) => [
                'name' => $rows->first()->category?->name ?? 'Lainnya',
                'amount' => $rows->sum('amount'),
            ])
            ->sortByDesc('amount')
            ->first();
        $weeklyRecap = [
            'income' => $weeklyIncome,
            'expense' => $weeklyExpense,
            'transaction_count' => $weeklyTransactions->count(),
            'top_category' => $weeklyTopCategory,
        ];

        $goalRecommendations = collect();
        foreach ($couple->targets()->where('status', 'active')->get() as $target) {
            if ($target->deadline && $target->remaining > 0) {
                $monthsLeft = max(1, now()->diffInMonths($target->deadline, false) + 1);
                $goalRecommendations->push([
                    'title' => $target->name,
                    'description' => 'Agar tercapai pada ' . $target->deadline->isoFormat('MMMM Y') . ', sisihkan sekitar Rp ' . number_format($target->remaining / $monthsLeft, 0, ',', '.') . ' per bulan.',
                ]);
            }
        }

        if ($expenseByCategory->isNotEmpty()) {
            $topExpense = $expenseByCategory->first();
            $goalRecommendations->push([
                'title' => 'Pola pengeluaran',
                'description' => 'Kategori terbesar bulan ini: ' . $topExpense['name'] . ' sebesar Rp ' . number_format($topExpense['amount'], 0, ',', '.') . '. Cek apakah perlu dibuat budget khusus.',
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
            'totalAssets',
            'totalWealthIncludingPiutang',
            'netWorth',
            'outstandingHutang',
            'outstandingPiutang',
            'targets',
            'debtReminders',
            'billReminders',
            'chartData',
            'expenseByCategory',
            'achievementBadges',
            'budgetNotifications',
            'goalRecommendations',
            'weeklyRecap'
        ));
    }
}

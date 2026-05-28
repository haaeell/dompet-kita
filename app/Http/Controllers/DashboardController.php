<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Bank;
use App\Models\Target;
use App\Models\TargetSaving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $incomeQuery = $couple->transactions()->where('type', 'income');
        $expenseQuery = $couple->transactions()->where('type', 'expense');

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
        $targets = $couple->targets()->where('status', 'active')->latest()->take(3)->get();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);

            $dayIncome = $couple->transactions()->where('type', 'income')->whereDate('date', $date->toDateString());
            $dayExpense = $couple->transactions()->where('type', 'expense')->whereDate('date', $date->toDateString());

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

        return view('dashboard.index', compact(
            'couple',
            'coupleMembers',
            'selectedUserId',
            'transactions',
            'monthlyIncome',
            'monthlyExpense',
            'banks',
            'totalWealth',
            'outstandingHutang',
            'outstandingPiutang',
            'targets',
            'chartData',
            'expenseByCategory'
        ));
    }
}

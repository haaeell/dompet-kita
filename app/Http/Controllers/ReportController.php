<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $couple = $user->couple;

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $userFilter = $request->user_filter ?? 'all';
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $partnerIds = $couple->users->where('id', '!=', $user->id)->pluck('id');

        $transactionQuery = $couple->transactions()
            ->with(['user', 'category', 'bank'])
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($userFilter === 'me') {
            $transactionQuery->where('user_id', $user->id);
        } elseif ($userFilter === 'partner') {
            $transactionQuery->whereIn('user_id', $partnerIds);
        }

        $transactions = $transactionQuery->latest('date')->get();
        $summaryTransactions = $transactions->reject(
            fn($transaction) => $transaction->category?->name === \App\Models\Transaction::TRANSFER_CATEGORY
        );

        $totalIncome = $summaryTransactions->where('type', 'income')->sum('amount');
        $totalExpense = $summaryTransactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $banksQuery = $couple->banks()->where('is_active', true);
        $debtsQuery = $couple->debts()
            ->with(['bank', 'user'])
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($userFilter === 'me') {
            $banksQuery->where('account_name', $user->name);
            $debtsQuery->where('user_id', $user->id);
        } elseif ($userFilter === 'partner') {
            $partnerNames = $couple->users->where('id', '!=', $user->id)->pluck('name');
            $banksQuery->whereIn('account_name', $partnerNames);
            $debtsQuery->whereIn('user_id', $partnerIds);
        }

        $totalWealth = $banksQuery->sum('current_balance');
        $outstandingHutang = (clone $debtsQuery)->where('type', 'hutang')->where('status', 'pending')->sum('amount');
        $outstandingPiutang = (clone $debtsQuery)->where('type', 'piutang')->where('status', 'pending')->sum('amount');
        $totalWealthIncludingPiutang = $totalWealth + $outstandingPiutang;
        $debts = $debtsQuery->latest('due_date')->get();

        $userSummary = $couple->users->map(function ($u) use ($summaryTransactions) {
            return [
                'user' => $u,
                'income' => $summaryTransactions->where('user_id', $u->id)->where('type', 'income')->sum('amount'),
                'expense' => $summaryTransactions->where('user_id', $u->id)->where('type', 'expense')->sum('amount'),
            ];
        });

        $expenseByCategory = $summaryTransactions->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn($group) => [
                'name' => $group->first()->category->name,
                'icon' => $group->first()->category->icon,
                'color' => $group->first()->category->color,
                'amount' => $group->sum('amount'),
                'count' => $group->count(),
            ])->values()->sortByDesc('amount');

        $monthlyTrend = [];
        $dayCount = $startDate->diffInDays($endDate) + 1;

        if ($dayCount <= 31) {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $trendIncomeQuery = $couple->transactions()
                    ->nonTransfer()
                    ->where('type', 'income')
                    ->whereDate('date', $date->toDateString());
                $trendExpenseQuery = $couple->transactions()
                    ->nonTransfer()
                    ->where('type', 'expense')
                    ->whereDate('date', $date->toDateString());

                if ($userFilter === 'me') {
                    $trendIncomeQuery->where('user_id', $user->id);
                    $trendExpenseQuery->where('user_id', $user->id);
                } elseif ($userFilter === 'partner') {
                    $trendIncomeQuery->whereIn('user_id', $partnerIds);
                    $trendExpenseQuery->whereIn('user_id', $partnerIds);
                }

                $monthlyTrend[] = [
                    'label' => $date->isoFormat('D MMM'),
                    'income' => $trendIncomeQuery->sum('amount'),
                    'expense' => $trendExpenseQuery->sum('amount'),
                ];
            }
        } else {
            $trendStart = $startDate->copy()->startOfMonth();
            $trendEnd = $endDate->copy()->startOfMonth();

            while ($trendStart->lte($trendEnd)) {
                $trendIncomeQuery = $couple->transactions()
                    ->nonTransfer()
                    ->where('type', 'income')
                    ->whereMonth('date', $trendStart->month)
                    ->whereYear('date', $trendStart->year);
                $trendExpenseQuery = $couple->transactions()
                    ->nonTransfer()
                    ->where('type', 'expense')
                    ->whereMonth('date', $trendStart->month)
                    ->whereYear('date', $trendStart->year);

                if ($userFilter === 'me') {
                    $trendIncomeQuery->where('user_id', $user->id);
                    $trendExpenseQuery->where('user_id', $user->id);
                } elseif ($userFilter === 'partner') {
                    $trendIncomeQuery->whereIn('user_id', $partnerIds);
                    $trendExpenseQuery->whereIn('user_id', $partnerIds);
                }

                $monthlyTrend[] = [
                    'label' => $trendStart->isoFormat('MMM Y'),
                    'income' => $trendIncomeQuery->sum('amount'),
                    'expense' => $trendExpenseQuery->sum('amount'),
                ];

                $trendStart->addMonth();
            }
        }

        return view('reports.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'totalWealth',
            'totalWealthIncludingPiutang',
            'outstandingHutang',
            'outstandingPiutang',
            'debts',
            'userSummary',
            'expenseByCategory',
            'monthlyTrend',
            'month',
            'year',
            'userFilter',
            'startDate',
            'endDate'
        ));
    }
}

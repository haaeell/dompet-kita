<?php

namespace App\Http\Controllers;

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

        $transactionQuery = $couple->transactions()
            ->with(['user', 'category', 'bank'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($userFilter === 'me') {
            $transactionQuery->where('user_id', $user->id);
        } elseif ($userFilter === 'partner') {
            $partnerIds = $couple->users->where('id', '!=', $user->id)->pluck('id');
            $transactionQuery->whereIn('user_id', $partnerIds);
        }

        $transactions = $transactionQuery->latest('date')->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        $userSummary = $couple->users->map(function ($u) use ($transactions) {
            return [
                'user' => $u,
                'income' => $transactions->where('user_id', $u->id)->where('type', 'income')->sum('amount'),
                'expense' => $transactions->where('user_id', $u->id)->where('type', 'expense')->sum('amount'),
            ];
        });

        $expenseByCategory = $transactions->where('type', 'expense')
            ->groupBy('category_id')
            ->map(fn($group) => [
                'name' => $group->first()->category->name,
                'icon' => $group->first()->category->icon,
                'color' => $group->first()->category->color,
                'amount' => $group->sum('amount'),
                'count' => $group->count(),
            ])->values()->sortByDesc('amount');

        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $trendIncomeQuery = $couple->transactions()->where('type', 'income')->whereMonth('date', $date->month)->whereYear('date', $date->year);
            $trendExpenseQuery = $couple->transactions()->where('type', 'expense')->whereMonth('date', $date->month)->whereYear('date', $date->year);

            if ($userFilter === 'me') {
                $trendIncomeQuery->where('user_id', $user->id);
                $trendExpenseQuery->where('user_id', $user->id);
            } elseif ($userFilter === 'partner') {
                $partnerIds = $couple->users->where('id', '!=', $user->id)->pluck('id');
                $trendIncomeQuery->whereIn('user_id', $partnerIds);
                $trendExpenseQuery->whereIn('user_id', $partnerIds);
            }

            $monthlyTrend[] = [
                'label' => $date->format('M Y'),
                'income' => $trendIncomeQuery->sum('amount'),
                'expense' => $trendExpenseQuery->sum('amount'),
            ];
        }

        return view('reports.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'balance',
            'userSummary',
            'expenseByCategory',
            'monthlyTrend',
            'month',
            'year',
            'userFilter'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CategoryBudget;
use App\Models\ChatMessage;
use App\Models\Couple;
use App\Models\Debt;
use App\Models\Target;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $stats = [
            'users' => User::where('role', '!=', 'admin')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'couples' => Couple::count(),
            'complete_couples' => Couple::has('users', '>=', 2)->count(),
            'waiting_couples' => Couple::has('users', '=', 1)->count(),
            'transactions' => Transaction::nonTransfer()->count(),
            'banks' => Bank::count(),
            'targets_active' => Target::where('status', 'active')->count(),
            'debts_unpaid' => Debt::where('status', 'unpaid')->count(),
            'chat_messages' => ChatMessage::count(),
        ];

        $monthly = [
            'new_users' => User::where('role', '!=', 'admin')->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'new_couples' => Couple::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            'transaction_count' => Transaction::nonTransfer()->whereBetween('date', [$monthStart, $monthEnd])->count(),
            'income' => Transaction::nonTransfer()->where('type', 'income')->whereBetween('date', [$monthStart, $monthEnd])->sum('amount'),
            'expense' => Transaction::nonTransfer()->where('type', 'expense')->whereBetween('date', [$monthStart, $monthEnd])->sum('amount'),
        ];

        $sessions = [
            'online' => 0,
            'last_day' => 0,
        ];

        if (Schema::hasTable('sessions')) {
            $sessions = [
                'online' => DB::table('sessions')->where('last_activity', '>=', $now->copy()->subMinutes(15)->timestamp)->count(),
                'last_day' => DB::table('sessions')->where('last_activity', '>=', $now->copy()->subDay()->timestamp)->count(),
            ];
        }

        $budgetHealth = $this->budgetHealth($monthStart, $monthEnd);
        $activityChart = $this->activityChart();

        $latestCouples = Couple::with([
            'users:id,couple_id,name,email,role,created_at',
        ])
            ->withCount(['users', 'transactions', 'banks', 'targets'])
            ->latest()
            ->take(8)
            ->get();

        $recentUsers = User::with('couple:id,couple_name')
            ->where('role', '!=', 'admin')
            ->latest()
            ->take(8)
            ->get(['id', 'couple_id', 'name', 'email', 'role', 'created_at']);

        $recentTransactions = Transaction::with([
            'couple:id,couple_name',
            'user:id,name',
            'category:id,name,icon',
        ])
            ->nonTransfer()
            ->latest('date')
            ->take(8)
            ->get();

        $topCouples = Couple::query()
            ->withCount('transactions')
            ->withSum([
                'transactions as monthly_expense' => fn ($query) => $query
                    ->nonTransfer()
                    ->where('type', 'expense')
                    ->whereBetween('date', [$monthStart, $monthEnd]),
            ], 'amount')
            ->withSum([
                'transactions as monthly_income' => fn ($query) => $query
                    ->nonTransfer()
                    ->where('type', 'income')
                    ->whereBetween('date', [$monthStart, $monthEnd]),
            ], 'amount')
            ->orderByDesc('transactions_count')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthly',
            'sessions',
            'budgetHealth',
            'activityChart',
            'latestCouples',
            'recentUsers',
            'recentTransactions',
            'topCouples'
        ));
    }

    private function budgetHealth($monthStart, $monthEnd): array
    {
        $budgets = CategoryBudget::with('category:id,name,icon')
            ->whereDate('budget_month', $monthStart->toDateString())
            ->get();

        $overBudget = 0;
        $nearLimit = 0;

        foreach ($budgets as $budget) {
            $spent = Transaction::nonTransfer()
                ->where('couple_id', $budget->couple_id)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $ratio = $budget->amount > 0 ? ($spent / $budget->amount) : 0;

            if ($ratio >= 1) {
                $overBudget++;
            } elseif ($ratio >= 0.8) {
                $nearLimit++;
            }
        }

        return [
            'active' => $budgets->count(),
            'over' => $overBudget,
            'near_limit' => $nearLimit,
        ];
    }

    private function activityChart(): array
    {
        $start = now()->copy()->subDays(13)->startOfDay();

        $activity = Transaction::nonTransfer()
            ->selectRaw('DATE(date) as activity_date, COUNT(*) as total')
            ->where('date', '>=', $start)
            ->groupBy('activity_date')
            ->pluck('total', 'activity_date');

        $labels = [];
        $values = [];

        foreach (CarbonPeriod::create($start, now()->startOfDay()) as $date) {
            $key = $date->toDateString();
            $labels[] = $date->format('d/m');
            $values[] = (int) ($activity[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}

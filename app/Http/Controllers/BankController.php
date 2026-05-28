<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function index()
    {
        $couple = Auth::user()->couple;
        $banks = $couple->banks()->withCount('transactions')->get();
        return view('banks.index', compact('banks'));
    }

    public function mutations(Request $request, Bank $bank)
    {
        $this->authorizeBank($bank);

        $baseQuery = $this->buildMutationQuery($request, $bank);

        $transactions = (clone $baseQuery)
            ->latest('date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();
        $transactions->setCollection($this->attachRunningBalances($bank, $transactions->getCollection()));

        $incomeTotal = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $expenseTotal = (clone $baseQuery)->where('type', 'expense')->sum('amount');

        return view('banks.mutations', compact('bank', 'transactions', 'incomeTotal', 'expenseTotal'));
    }

    public function mutationsPdf(Request $request, Bank $bank)
    {
        $this->authorizeBank($bank);

        $baseQuery = $this->buildMutationQuery($request, $bank);
        $transactions = $this->attachRunningBalances(
            $bank,
            (clone $baseQuery)->orderBy('date')->orderBy('id')->get()
        );
        $incomeTotal = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $expenseTotal = (clone $baseQuery)->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('banks.mutations-pdf', [
            'bank' => $bank,
            'transactions' => $transactions,
            'incomeTotal' => $incomeTotal,
            'expenseTotal' => $expenseTotal,
            'filters' => [
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $filename = 'mutasi_' . str($bank->name)->slug('_') . '_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'icon' => 'required|string|max:10',
            'color' => 'required|string|size:7',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $bank = Auth::user()->couple->banks()->create(array_merge(
            $request->only(['name', 'account_name', 'account_number', 'icon', 'color', 'initial_balance']),
            ['current_balance' => $request->initial_balance]
        ));

        return response()->json(['success' => true, 'message' => 'Rekening berhasil ditambahkan!', 'bank' => $bank]);
    }

    public function update(Request $request, Bank $bank)
    {
        $this->authorize('update', $bank);
        $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'icon' => 'required|string',
            'color' => 'required|string|size:7',
        ]);
        $bank->update($request->only(['name', 'account_name', 'account_number', 'icon', 'color']));
        return response()->json(['success' => true, 'message' => 'Rekening berhasil diperbarui!']);
    }

    public function destroy(Bank $bank)
    {
        $this->authorize('delete', $bank);
        if ($bank->transactions()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Rekening tidak bisa dihapus karena masih ada transaksi!'], 422);
        }
        $bank->delete();
        return response()->json(['success' => true, 'message' => 'Rekening berhasil dihapus!']);
    }

    protected function authorizeBank(Bank $bank): void
    {
        if ($bank->couple_id !== Auth::user()->couple_id) {
            abort(403);
        }
    }

    protected function buildMutationQuery(Request $request, Bank $bank): Builder
    {
        $query = $bank->transactions()->with(['user', 'category']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        return $query;
    }

    protected function attachRunningBalances(Bank $bank, Collection $transactions): Collection
    {
        $runningBalance = (float) $bank->initial_balance;
        $balanceMap = [];

        $bank->transactions()
            ->select(['id', 'type', 'amount'])
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->each(function ($transaction) use (&$runningBalance, &$balanceMap) {
                $openingBalance = $runningBalance;
                $delta = $transaction->type === 'income'
                    ? (float) $transaction->amount
                    : -(float) $transaction->amount;
                $closingBalance = $openingBalance + $delta;

                $balanceMap[$transaction->id] = [
                    'opening_balance' => $openingBalance,
                    'delta' => $delta,
                    'closing_balance' => $closingBalance,
                ];

                $runningBalance = $closingBalance;
            });

        return $transactions->map(function ($transaction) use ($balanceMap) {
            $transaction->opening_balance = $balanceMap[$transaction->id]['opening_balance'] ?? 0;
            $transaction->balance_delta = $balanceMap[$transaction->id]['delta'] ?? 0;
            $transaction->closing_balance = $balanceMap[$transaction->id]['closing_balance'] ?? 0;

            return $transaction;
        });
    }
}

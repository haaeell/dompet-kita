<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        $couple = Auth::user()->couple;
        $banks = $couple->banks()->withCount('transactions')->get();
        return view('banks.index', compact('banks'));
    }

    public function transfer()
    {
        $banks = Auth::user()->couple
            ->banks()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('banks.transfer', compact('banks'));
    }

    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_bank_id' => 'required|exists:banks,id',
            'to_bank_id' => 'required|exists:banks,id|different:from_bank_id',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $couple = Auth::user()->couple;
        $fromBank = $couple->banks()->where('is_active', true)->findOrFail($request->from_bank_id);
        $toBank = $couple->banks()->where('is_active', true)->findOrFail($request->to_bank_id);
        $amount = (float) $request->amount;

        if ($fromBank->current_balance < $amount) {
            return back()
                ->withInput()
                ->with('error', 'Saldo rekening asal tidak cukup untuk transfer.');
        }

        DB::transaction(function () use ($request, $couple, $fromBank, $toBank, $amount) {
            $expenseCategory = $this->getTransferCategory($couple, 'expense');
            $incomeCategory = $this->getTransferCategory($couple, 'income');
            $transferCode = 'TRF-' . now()->format('YmdHis') . '-' . Auth::id();
            $notes = trim((string) $request->notes);

            Transaction::create([
                'couple_id' => $couple->id,
                'user_id' => Auth::id(),
                'category_id' => $expenseCategory->id,
                'bank_id' => $fromBank->id,
                'type' => 'expense',
                'amount' => $amount,
                'description' => 'Transfer ke ' . $toBank->name,
                'notes' => trim($transferCode . ($notes ? ' - ' . $notes : '')),
                'date' => $request->date,
            ]);

            Transaction::create([
                'couple_id' => $couple->id,
                'user_id' => Auth::id(),
                'category_id' => $incomeCategory->id,
                'bank_id' => $toBank->id,
                'type' => 'income',
                'amount' => $amount,
                'description' => 'Transfer dari ' . $fromBank->name,
                'notes' => trim($transferCode . ($notes ? ' - ' . $notes : '')),
                'date' => $request->date,
            ]);
        });

        return redirect()
            ->route('banks.index')
            ->with('success', 'Transfer antar bank berhasil dicatat.');
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

    protected function getTransferCategory($couple, string $type): Category
    {
        return Category::firstOrCreate([
            'couple_id' => $couple->id,
            'name' => Transaction::TRANSFER_CATEGORY,
            'type' => $type,
        ], [
            'icon' => '🔁',
            'color' => '#3b82f6',
            'is_default' => false,
        ]);
    }

    protected function buildMutationQuery(Request $request, Bank $bank): HasMany
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

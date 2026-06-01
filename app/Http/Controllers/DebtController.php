<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $couple = $user->couple;
        $type = $request->get('type', 'hutang');
        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? $couple->users()->find($selectedUserId) : null;

        $debtsQuery = $couple->debts()
            ->with(['bank', 'settlementBank', 'user'])
            ->where('type', $type);

        if ($selectedUser) {
            $debtsQuery->where('user_id', $selectedUser->id);
        }

        $debts = $debtsQuery->latest('due_date')->get();

        $banksQuery = $couple->banks()->where('is_active', true);
        if ($selectedUser) {
            $banksQuery->where('user_id', $selectedUser->id);
        }

        $banks = $banksQuery->get();
        $coupleMembers = $couple->users;
        $totalWealth = $banks->sum('current_balance');
        $debtSummaryQuery = $couple->debts();
        if ($selectedUser) {
            $debtSummaryQuery->where('user_id', $selectedUser->id);
        }

        $outstandingHutang = (clone $debtSummaryQuery)
            ->where('type', 'hutang')
            ->where('status', 'pending')
            ->sum(DB::raw('amount - paid_amount'));
        $outstandingPiutang = (clone $debtSummaryQuery)
            ->where('type', 'piutang')
            ->where('status', 'pending')
            ->sum(DB::raw('amount - paid_amount'));

        return view('debts.index', compact(
            'debts',
            'banks',
            'type',
            'totalWealth',
            'outstandingHutang',
            'outstandingPiutang',
            'coupleMembers',
            'selectedUserId'
        ));
    }

    public function store(Request $request)
    {
        $couple = Auth::user()->couple;

        $request->validate([
            'type' => 'required|in:hutang,piutang',
            'amount' => 'required|numeric|min:1',
            'installment_count' => 'nullable|integer|min:1|max:120',
            'installment_amount' => 'nullable|numeric|min:1',
            'counterparty' => 'required|string|max:255',
            'purpose' => 'required|string|max:255',
            'due_date' => 'required|date',
            'bank_id' => 'required|exists:banks,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $bank = $couple->banks()->findOrFail($request->bank_id);
        $installmentCount = max(1, (int) $request->input('installment_count', 1));
        $installmentAmount = (float) ($request->input('installment_amount') ?: ceil((float) $request->amount / $installmentCount));

        $debt = Debt::create([
            'couple_id' => $couple->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'installment_count' => $installmentCount,
            'installment_amount' => min((float) $request->amount, $installmentAmount),
            'paid_amount' => 0,
            'counterparty' => $request->counterparty,
            'purpose' => $request->purpose,
            'due_date' => $request->due_date,
            'bank_id' => $request->bank_id,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        $category = $this->getDebtCategory($couple, $request->type);
        $transactionType = $request->type === 'hutang' ? 'income' : 'expense';
        $description = $request->type === 'hutang'
            ? 'Dana hutang dari ' . $request->counterparty
            : 'Piutang ke ' . $request->counterparty;

        $transaction = Transaction::create([
            'couple_id' => $couple->id,
            'user_id' => Auth::id(),
            'category_id' => $category->id,
            'bank_id' => $bank->id,
            'type' => $transactionType,
            'amount' => $request->amount,
            'description' => $description,
            'notes' => $request->notes,
            'date' => now(),
        ]);

        $debt->initial_transaction_id = $transaction->id;
        $debt->save();

        return redirect()
            ->route('debts.index', ['type' => $request->type])
            ->with('success', 'Catatan ' . ($request->type === 'hutang' ? 'hutang' : 'piutang') . ' berhasil disimpan.');
    }

    public function pay(Request $request, Debt $debt)
    {
        $this->authorizeDebt($debt);

        $request->validate([
            'settlement_bank_id' => 'required|exists:banks,id',
            'paid_at' => 'required|date',
            'payment_amount' => 'nullable|numeric|min:1',
        ]);

        if ($debt->status === 'paid') {
            return back()->with('error', 'Catatan sudah dibayar atau dikembalikan.');
        }

        $settlementBank = $debt->couple->banks()->findOrFail($request->settlement_bank_id);
        $remainingAmount = $debt->remaining_amount;
        $paymentAmount = min(
            $remainingAmount,
            (float) ($request->input('payment_amount') ?: ($debt->installment_amount ?: $remainingAmount))
        );
        $newPaidAmount = min((float) $debt->amount, (float) $debt->paid_amount + $paymentAmount);
        $isFullyPaid = $newPaidAmount >= (float) $debt->amount;

        $debt->update([
            'settlement_bank_id' => $settlementBank->id,
            'last_payment_at' => $request->paid_at,
            'paid_at' => $isFullyPaid ? $request->paid_at : null,
            'paid_amount' => $newPaidAmount,
            'status' => $isFullyPaid ? 'paid' : 'pending',
        ]);

        $category = $this->getDebtCategory($debt->couple, $debt->type);
        $transactionType = $debt->type === 'hutang' ? 'expense' : 'income';
        $description = $debt->type === 'hutang'
            ? 'Bayar hutang ke ' . $debt->counterparty
            : 'Piutang kembali dari ' . $debt->counterparty;

        Transaction::create([
            'couple_id' => $debt->couple_id,
            'user_id' => $debt->user_id,
            'category_id' => $category->id,
            'bank_id' => $settlementBank->id,
            'type' => $transactionType,
            'amount' => $paymentAmount,
            'description' => $description . ($isFullyPaid ? ' (lunas)' : ' (cicilan)'),
            'notes' => 'Pembayaran cicilan ' . $debt->installment_progress . ' - ' . $debt->type,
            'date' => $request->paid_at,
        ]);

        return back()->with('success', $isFullyPaid
            ? 'Pembayaran hutang/piutang berhasil dicatat dan sudah lunas.'
            : 'Cicilan berhasil dicatat. Sisa Rp ' . number_format($debt->fresh()->remaining_amount, 0, ',', '.'));
    }

    public function destroy(Debt $debt)
    {
        $this->authorizeDebt($debt);

        if ($debt->status === 'paid') {
            return back()->with('error', 'Catatan yang sudah selesai tidak bisa dihapus.');
        }

        if ($debt->initial_transaction_id) {
            Transaction::find($debt->initial_transaction_id)?->delete();
        }

        $debt->delete();

        return back()->with('success', 'Catatan hutang/piutang berhasil dihapus.');
    }

    protected function getDebtCategory($couple, string $type): Category
    {
        $name = $type === 'hutang' ? 'Hutang' : 'Piutang';
        $categoryType = $type === 'hutang' ? 'income' : 'expense';
        $icon = '💸';
        $color = $type === 'hutang' ? '#10b981' : '#ef4444';

        return Category::firstOrCreate([
            'couple_id' => $couple->id,
            'name' => $name,
            'type' => $categoryType,
        ], [
            'icon' => $icon,
            'color' => $color,
            'is_default' => false,
        ]);
    }

    protected function authorizeDebt(Debt $debt): void
    {
        if ($debt->couple_id !== Auth::user()->couple_id) {
            abort(403);
        }
    }
}

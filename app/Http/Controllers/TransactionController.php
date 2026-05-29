<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $couple = Auth::user()->couple;
        $query = $couple->transactions()->with(['user', 'category', 'bank']);

        if ($request->type) $query->where('type', $request->type);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->bank_id) $query->where('bank_id', $request->bank_id);
        if ($request->month) $query->whereMonth('date', $request->month);
        if ($request->year) $query->whereYear('date', $request->year ?? now()->year);
        if ($request->search) $query->where('description', 'like', "%{$request->search}%");
        if ($request->user_id) $query->where('user_id', $request->user_id);

        $transactions = $query->latest('date')->paginate(15);
        $categories = $couple->categories()->orderBy('name')->get();
        $banks = $couple->banks()->where('is_active', true)->get();
        $coupleUsers = $couple->users()->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'banks', 'coupleUsers'));
    }

    public function create()
    {
        $couple = Auth::user()->couple;
        $categories = $couple->categories()->orderBy('type')->orderBy('name')->get();

        $banks = $couple->banks()
            ->where('is_active', true)
            ->where('account_name', Auth::user()->name)
            ->get();

        if ($banks->isEmpty()) {
            $banks = $couple->banks()->where('is_active', true)->get();
        }

        return view('transactions.create', compact('categories', 'banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'bank_id' => 'required|exists:banks,id',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'client_uuid' => 'nullable|string|max:80',
        ]);

        $couple = Auth::user()->couple;

        if ($request->filled('client_uuid')) {
            $existingTransaction = $couple->transactions()
                ->where('client_uuid', $request->client_uuid)
                ->with(['user', 'category', 'bank'])
                ->first();

            if ($existingTransaction) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi offline sudah tersinkron.',
                    'transaction' => $existingTransaction,
                ]);
            }
        }

        // Validasi category & bank milik couple ini
        $category = $couple->categories()->findOrFail($request->category_id);
        $bank = $couple->banks()->findOrFail($request->bank_id);

        $transaction = Transaction::create([
            'couple_id' => $couple->id,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'bank_id' => $request->bank_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'notes' => $request->notes,
            'date' => $request->date,
            'client_uuid' => $request->client_uuid,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan! 🎉',
                'transaction' => $transaction->load(['user', 'category', 'bank']),
            ]);
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan! 🎉');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'bank_id' => 'required|exists:banks,id',
            'date' => 'required|date',
        ]);

        $transaction->update($request->only([
            'amount',
            'description',
            'category_id',
            'bank_id',
            'date',
            'notes'
        ]));

        return response()->json(['success' => true, 'message' => 'Transaksi berhasil diperbarui!']);
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus!']);
    }
}

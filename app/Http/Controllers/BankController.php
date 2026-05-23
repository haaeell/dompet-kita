<?php

namespace App\Http\Controllers;

use App\Models\Bank;
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
}

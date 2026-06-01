<?php

namespace App\Http\Controllers;

use App\Models\BillReminder;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillReminderController extends Controller
{
    public function index()
    {
        $couple = Auth::user()->couple;
        $reminders = $couple->billReminders()
            ->with(['user', 'bank', 'category'])
            ->orderBy('is_paid')
            ->orderBy('due_date')
            ->get();
        $banks = $couple->banks()->where('is_active', true)->orderBy('name')->get();
        $categories = $couple->categories()->where('type', 'expense')->orderBy('name')->get();
        $members = $couple->users()->orderBy('name')->get();

        return view('bill-reminders.index', compact('reminders', 'banks', 'categories', 'members'));
    }

    public function store(Request $request)
    {
        $couple = Auth::user()->couple;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'repeat' => ['required', 'in:none,monthly,weekly,yearly'],
            'user_id' => ['nullable', 'exists:users,id'],
            'bank_id' => ['nullable', 'exists:banks,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data['user_id'] = filled($data['user_id'] ?? null)
            ? $couple->users()->findOrFail($data['user_id'])->id
            : Auth::id();
        $data['bank_id'] = filled($data['bank_id'] ?? null)
            ? $couple->banks()->findOrFail($data['bank_id'])->id
            : null;
        $data['category_id'] = filled($data['category_id'] ?? null)
            ? $couple->categories()->where('type', 'expense')->findOrFail($data['category_id'])->id
            : null;

        $couple->billReminders()->create($data);

        return back()->with('success', 'Reminder tagihan berhasil dibuat.');
    }

    public function markPaid(Request $request, BillReminder $billReminder)
    {
        $this->authorizeReminder($billReminder);

        $request->validate([
            'create_transaction' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('create_transaction') && $billReminder->bank_id && $billReminder->category_id && $billReminder->amount > 0) {
            Transaction::create([
                'couple_id' => $billReminder->couple_id,
                'user_id' => $billReminder->user_id ?: Auth::id(),
                'category_id' => $billReminder->category_id,
                'bank_id' => $billReminder->bank_id,
                'type' => 'expense',
                'amount' => $billReminder->amount,
                'description' => 'Bayar tagihan ' . $billReminder->title,
                'notes' => $billReminder->notes,
                'date' => now(),
            ]);
        }

        $nextDate = match ($billReminder->repeat) {
            'weekly' => $billReminder->due_date->copy()->addWeek(),
            'monthly' => $billReminder->due_date->copy()->addMonthNoOverflow(),
            'yearly' => $billReminder->due_date->copy()->addYear(),
            default => null,
        };

        if ($nextDate) {
            $billReminder->update([
                'due_date' => $nextDate,
                'is_paid' => false,
                'paid_at' => now(),
            ]);
        } else {
            $billReminder->update([
                'is_paid' => true,
                'paid_at' => now(),
            ]);
        }

        return back()->with('success', 'Tagihan ditandai selesai.');
    }

    public function destroy(BillReminder $billReminder)
    {
        $this->authorizeReminder($billReminder);
        $billReminder->delete();

        return back()->with('success', 'Reminder tagihan berhasil dihapus.');
    }

    private function authorizeReminder(BillReminder $billReminder): void
    {
        abort_unless((int) $billReminder->couple_id === (int) Auth::user()->couple_id, 403);
    }
}

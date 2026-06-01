<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\TargetSaving;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TargetController extends Controller
{
    public function index()
    {
        $couple = Auth::user()->couple;

        $targets = $couple->targets()
            ->with(['savings' => function ($query) {
                $query->latest('date');
            }, 'savings.user'])
            ->orderBy('status')
            ->latest()
            ->get();

        $banks = $couple->banks;
        $expenseCategories = $couple->categories()
            ->where('type', 'expense')
            ->where('name', '!=', Transaction::TRANSFER_CATEGORY)
            ->orderBy('name')
            ->get();

        return view('targets.index', compact('targets', 'banks', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'target_amount' => str_replace(['.', ','], '', $request->target_amount),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:10',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'nullable|date|after:today',
            'color' => 'required|string|size:7',
        ]);

        $target = Auth::user()->couple->targets()->create($request->only([
            'name',
            'icon',
            'target_amount',
            'deadline',
            'color',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Target berhasil dibuat. Anggap target ini seperti amplop tujuan.',
            'target' => $target,
        ]);
    }

    public function destroy(Target $target)
    {
        $this->authorize('delete', $target);
        $target->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Target dibatalkan.']);
    }

    public function addSaving(Request $request, Target $target)
    {
        abort_unless($target->couple_id === Auth::user()->couple_id, 403);

        $request->merge([
            'amount' => str_replace(['.', ','], '', $request->amount),
        ]);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'source_bank_id' => 'required|exists:banks,id',
            'target_bank_id' => 'required|exists:banks,id',
            'notes' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        if ($request->source_bank_id == $request->target_bank_id) {
            return response()->json(['success' => false, 'message' => 'Rekening asal dan tujuan tidak boleh sama.'], 422);
        }

        DB::beginTransaction();
        try {
            $couple = Auth::user()->couple;
            $sourceBank = $couple->banks()->findOrFail($request->source_bank_id);
            $targetBank = $couple->banks()->findOrFail($request->target_bank_id);

            if ($sourceBank->current_balance < $request->amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Saldo rekening asal tidak mencukupi.'], 422);
            }

            TargetSaving::create([
                'target_id' => $target->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'notes' => $request->notes,
                'date' => $request->date,
            ]);

            $couple->transactions()->create([
                'user_id' => Auth::id(),
                'bank_id' => $sourceBank->id,
                'category_id' => $this->getOrCreateSavingCategory($couple, 'expense'),
                'type' => 'expense',
                'amount' => $request->amount,
                'description' => 'Setor target ' . $target->name . ' ke ' . $targetBank->name,
                'notes' => $request->notes,
                'date' => $request->date,
            ]);

            $couple->transactions()->create([
                'user_id' => Auth::id(),
                'bank_id' => $targetBank->id,
                'category_id' => $this->getOrCreateSavingCategory($couple, 'income'),
                'type' => 'income',
                'amount' => $request->amount,
                'description' => 'Dana target ' . $target->name . ' dari ' . $sourceBank->name,
                'notes' => $request->notes,
                'date' => $request->date,
            ]);

            $target->refresh();

            if ($target->current_amount >= $target->target_amount) {
                $target->update(['status' => 'completed']);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Selamat, target tercapai. Setoran ini hanya pindah uang antar rekening.',
                    'completed' => true,
                    'target' => $target,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setoran target berhasil. Ini hanya pindah uang antar rekening, bukan pengeluaran konsumtif.',
                'target' => $target,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Gagal memproses setoran target: ' . $e->getMessage()], 500);
        }
    }

    public function spend(Request $request, Target $target)
    {
        abort_unless($target->couple_id === Auth::user()->couple_id, 403);

        $request->merge([
            'amount' => str_replace(['.', ','], '', $request->amount),
        ]);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_id' => 'required|exists:banks,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $couple = Auth::user()->couple;
        $amount = (float) $request->amount;

        if ($amount > $target->current_amount) {
            return response()->json(['success' => false, 'message' => 'Dana target belum cukup untuk dipakai sebesar itu.'], 422);
        }

        DB::beginTransaction();
        try {
            $bank = $couple->banks()->findOrFail($request->bank_id);
            $category = $couple->categories()
                ->where('type', 'expense')
                ->findOrFail($request->category_id);

            if ($bank->current_balance < $amount) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Saldo rekening target tidak mencukupi.'], 422);
            }

            TargetSaving::create([
                'target_id' => $target->id,
                'user_id' => Auth::id(),
                'amount' => -$amount,
                'notes' => 'Pakai dana: ' . ($request->notes ?: $request->description),
                'date' => $request->date,
            ]);

            $couple->transactions()->create([
                'user_id' => Auth::id(),
                'bank_id' => $bank->id,
                'category_id' => $category->id,
                'type' => 'expense',
                'amount' => $amount,
                'description' => $request->description,
                'notes' => trim('Pakai dana target ' . $target->name . ($request->notes ? ' - ' . $request->notes : '')),
                'date' => $request->date,
            ]);

            $target->refresh();
            if ($target->current_amount < $target->target_amount && $target->status === 'completed') {
                $target->update(['status' => 'active']);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Dana target berhasil dipakai dan dicatat sebagai pengeluaran asli.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Gagal memakai dana target: ' . $e->getMessage()], 500);
        }
    }

    private function getOrCreateSavingCategory($couple, string $type): int
    {
        $category = $couple->categories()
            ->where('name', Transaction::TRANSFER_CATEGORY)
            ->where('type', $type)
            ->first();

        if (! $category) {
            $category = $couple->categories()->create([
                'name' => Transaction::TRANSFER_CATEGORY,
                'icon' => '🔁',
                'color' => '#3b82f6',
                'type' => $type,
            ]);
        }

        return $category->id;
    }
}

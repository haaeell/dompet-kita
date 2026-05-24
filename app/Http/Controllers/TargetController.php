<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\TargetSaving;
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

        return view('targets.index', compact('targets', 'banks'));
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
            'color'
        ]));

        return response()->json(['success' => true, 'message' => 'Target berhasil dibuat! Semangat menabung! 💪', 'target' => $target]);
    }

    public function destroy(Target $target)
    {
        $this->authorize('delete', $target);
        $target->update(['status' => 'cancelled']);
        return response()->json(['success' => true, 'message' => 'Target dibatalkan.']);
    }

    public function addSaving(Request $request, Target $target)
    {
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
                return response()->json(['success' => false, 'message' => 'Saldo rekening asal tidak mencukupi!'], 422);
            }

            $sourceBank->decrement('current_balance', $request->amount);
            $targetBank->increment('current_balance', $request->amount);

            $saving = TargetSaving::create([
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
                'description' => "Pindah dana ke " . $targetBank->name . " untuk target: " . $target->name . ($request->notes ? " (" . $request->notes . ")" : ""),
                'date' => $request->date,
            ]);

            $couple->transactions()->create([
                'user_id' => Auth::id(),
                'bank_id' => $targetBank->id,
                'category_id' => $this->getOrCreateSavingCategory($couple, 'income'),
                'type' => 'income',
                'amount' => $request->amount,
                'description' => "Terima dana dari " . $sourceBank->name . " untuk target: " . $target->name . ($request->notes ? " (" . $request->notes . ")" : ""),
                'date' => $request->date,
            ]);

            DB::commit();

            $target->refresh();

            if ($target->current_amount >= $target->target_amount) {
                $target->update(['status' => 'completed']);
                return response()->json([
                    'success' => true,
                    'message' => '🎉 Selamat! Target berhasil tercapai!',
                    'completed' => true,
                    'target' => $target,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Dana berhasil dipindahkan antar rekening dan tabungan target diperbarui!', 'target' => $target]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses transfer tabungan: ' . $e->getMessage()], 500);
        }
    }
    private function getOrCreateSavingCategory($couple, $type)
    {
        $category = $couple->categories()->where('name', 'Tabungan')->where('type', $type)->first();
        if (!$category) {
            $category = $couple->categories()->create([
                'name' => 'Tabungan',
                'icon' => '🐷',
                'color' => $type == 'expense' ? '#db2777' : '#10b981',
                'type' => $type
            ]);
        }
        return $category->id;
    }
}

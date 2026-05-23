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
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_id' => 'required|exists:banks,id',
            'notes' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $saving = TargetSaving::create([
                'target_id' => $target->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'notes' => $request->notes,
                'date' => $request->date,
            ]);

            $target->couple->transactions()->create([
                'user_id' => Auth::id(),
                'bank_id' => $request->bank_id,
                'category_id' => $this->getOrCreateSavingCategory($target->couple),
                'type' => 'expense',
                'amount' => $request->amount,
                'description' => "Menabung untuk: " . $target->name . ($request->notes ? " (" . $request->notes . ")" : ""),
                'date' => $request->date,
            ]);

            $target->increment('current_amount', $request->amount);

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

            return response()->json(['success' => true, 'message' => 'Tabungan berhasil ditambahkan dan saldo bank terpotong!', 'target' => $target]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memproses tabungan: ' . $e->getMessage()], 500);
        }
    }

    private function getOrCreateSavingCategory($couple)
    {
        $category = $couple->categories()->where('name', 'Tabungan')->first();
        if (!$category) {
            $category = $couple->categories()->create([
                'name' => 'Tabungan',
                'icon' => '🐷',
                'color' => '#db2777',
                'type' => 'expense'
            ]);
        }
        return $category->id;
    }
}

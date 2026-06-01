<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $couple = Auth::user()->couple;
        $selectedUserId = $request->get('user_id');
        $selectedUser = $selectedUserId ? $couple->users()->find($selectedUserId) : null;

        $assetsQuery = $couple->assets()->with('user')->where('is_active', true);
        $banksQuery = $couple->banks()->where('is_active', true);
        $debtQuery = $couple->debts()->where('status', 'pending');

        if ($selectedUser) {
            $assetsQuery->where('user_id', $selectedUser->id);
            $banksQuery->where('user_id', $selectedUser->id);
            $debtQuery->where('user_id', $selectedUser->id);
        }

        $assets = $assetsQuery->latest()->get();
        $members = $couple->users()->orderBy('name')->get();
        $cashTotal = (float) $banksQuery->sum('current_balance');
        $assetTotal = (float) $assets->sum('current_value');
        $hutangTotal = (float) (clone $debtQuery)->where('type', 'hutang')->sum(DB::raw('amount - paid_amount'));
        $piutangTotal = (float) (clone $debtQuery)->where('type', 'piutang')->sum(DB::raw('amount - paid_amount'));
        $netWorth = $cashTotal + $assetTotal + $piutangTotal - $hutangTotal;

        $assetByType = $assets
            ->groupBy('type')
            ->map(fn ($rows, $type) => [
                'type' => $type,
                'total' => $rows->sum('current_value'),
                'count' => $rows->count(),
            ])
            ->sortByDesc('total')
            ->values();

        return view('assets.index', compact(
            'assets',
            'members',
            'selectedUserId',
            'cashTotal',
            'assetTotal',
            'hutangTotal',
            'piutangTotal',
            'netWorth',
            'assetByType'
        ));
    }

    public function store(Request $request)
    {
        $couple = Auth::user()->couple;
        $data = $this->validated($request);
        $data['user_id'] = filled($data['user_id'] ?? null)
            ? $couple->users()->findOrFail($data['user_id'])->id
            : Auth::id();
        $data['current_value'] = $data['current_value'] ?: $data['purchase_value'];

        $couple->assets()->create($data);

        return back()->with('success', 'Aset berhasil ditambahkan.');
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAsset($asset);
        $couple = Auth::user()->couple;
        $data = $this->validated($request);
        $data['user_id'] = filled($data['user_id'] ?? null)
            ? $couple->users()->findOrFail($data['user_id'])->id
            : $asset->user_id;
        $data['current_value'] = $data['current_value'] ?: $data['purchase_value'];

        $asset->update($data);

        return back()->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $this->authorizeAsset($asset);
        $asset->update(['is_active' => false]);

        return back()->with('success', 'Aset berhasil diarsipkan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:60'],
            'user_id' => ['nullable', 'exists:users,id'],
            'purchase_value' => ['required', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'acquired_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function authorizeAsset(Asset $asset): void
    {
        abort_unless((int) $asset->couple_id === (int) Auth::user()->couple_id, 403);
    }
}

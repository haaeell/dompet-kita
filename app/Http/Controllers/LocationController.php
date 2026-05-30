<?php

namespace App\Http\Controllers;

use App\Models\PartnerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $couple = $user->couple;

        $locations = PartnerLocation::with('user')
            ->where('couple_id', $couple->id)
            ->get()
            ->keyBy('user_id');

        $members = $couple->users()
            ->orderByRaw('id = ? desc', [$user->id])
            ->orderBy('name')
            ->get();

        return view('locations.index', compact('members', 'locations'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:50000',
            'label' => 'nullable|string|max:80',
        ]);

        $user = Auth::user();

        $location = PartnerLocation::updateOrCreate([
            'couple_id' => $user->couple_id,
            'user_id' => $user->id,
        ], [
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'accuracy' => $data['accuracy'] ?? null,
            'label' => $data['label'] ?: 'Lagi di sini',
            'is_active' => true,
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil dibagikan.',
            'location' => $location->load('user'),
        ]);
    }

}

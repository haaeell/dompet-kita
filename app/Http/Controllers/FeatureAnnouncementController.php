<?php

namespace App\Http\Controllers;

use App\Models\FeatureAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureAnnouncementController extends Controller
{
    public function dismiss(Request $request, FeatureAnnouncement $featureAnnouncement)
    {
        abort_unless($featureAnnouncement->is_active, 404);

        Auth::user()->readFeatureAnnouncements()->syncWithoutDetaching([
            $featureAnnouncement->id => ['read_at' => now()],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Update ditandai sudah dibaca.']);
        }

        return back()->with('success', 'Update ditandai sudah dibaca.');
    }
}

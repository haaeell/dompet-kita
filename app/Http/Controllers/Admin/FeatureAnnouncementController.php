<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureAnnouncement;
use Illuminate\Http\Request;

class FeatureAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = FeatureAnnouncement::withCount('reads')
            ->latest('published_at')
            ->latest()
            ->get();

        return view('admin.feature-announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        FeatureAnnouncement::create($this->validatedData($request));

        return back()->with('success', 'Pengumuman update berhasil dibuat dan akan muncul ke user.');
    }

    public function update(Request $request, FeatureAnnouncement $featureAnnouncement)
    {
        $featureAnnouncement->update($this->validatedData($request));

        return back()->with('success', 'Pengumuman update berhasil diperbarui.');
    }

    public function destroy(FeatureAnnouncement $featureAnnouncement)
    {
        $featureAnnouncement->delete();

        return back()->with('success', 'Pengumuman update berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:feature,improvement,fix,info'],
            'version' => ['nullable', 'string', 'max:40'],
            'body' => ['required', 'string', 'max:1200'],
            'published_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['published_at'] = $data['published_at'] ?? now();

        return $data;
    }
}

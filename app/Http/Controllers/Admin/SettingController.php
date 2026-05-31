<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $maintenanceMode = setting('maintenance_mode', '0');

        return view('admin.settings.index', compact('maintenanceMode'));
    }

    public function toggleMaintenance()
    {
        $setting = Setting::firstOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '0']
        );

        $setting->update([
            'value' => $setting->value === '1' ? '0' : '1',
        ]);

        return back()->with('success', 'Status maintenance berhasil diubah.');
    }
}

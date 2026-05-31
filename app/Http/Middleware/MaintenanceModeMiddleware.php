<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Schema::hasTable('settings')) {
            return $next($request);
        }

        $isMaintenance = Setting::where('key', 'maintenance_mode')->value('value') === '1';

        if (! $isMaintenance) {
            return $next($request);
        }

        if (
            auth()->check()
            && auth()->user()->role === 'admin'
        ) {
            return $next($request);
        }

        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        return response()->view('maintenance', [], 503);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
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

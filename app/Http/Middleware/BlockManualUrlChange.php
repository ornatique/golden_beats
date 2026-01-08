<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockManualUrlChange
{
    public function handle(Request $request, Closure $next)
    {
        // Allow AJAX & API requests
        if ($request->ajax() || $request->expectsJson()) {
            return $next($request);
        }

        // Ignore login & dashboard
        if (
            $request->routeIs('login') ||
            $request->routeIs('admin.dashboard')
        ) {
            return $next($request);
        }

        //  If ANY query string exists → redirect
        if (!empty($request->query())) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}


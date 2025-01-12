<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckIfUserHasApprovedTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($request->routeIs('filament.app.auth.logout')) {
            return $next($request);
        }

        if (!$user || !$user->tenants()->wherePivot('is_active', true)->exists()) {
            return redirect()->route('auth.tenant-missing');
        }

        return $next($request);
    }
}

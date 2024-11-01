<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ApplyTenantScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $tenant = Filament::getTenant() ?? Auth::user()->tenants()->first();

        if ($tenant) {
            User::addGlobalScope(
                fn (Builder $query) => $query->whereHas('tenants', function($query) use ($tenant) {
                    $query->where('id', $tenant->id);
                })
            );
        }

        return $next($request);
    }
}

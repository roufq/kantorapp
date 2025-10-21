<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = $request->user();
        // Support comma or pipe-delimited roles passed as a single arg
        if (count($roles) === 1 && is_string($roles[0]) && (str_contains($roles[0], ',') || str_contains($roles[0], '|'))) {
            $roles = preg_split('/[|,]/', $roles[0]);
        }

        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

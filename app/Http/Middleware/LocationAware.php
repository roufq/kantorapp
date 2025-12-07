<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LocationAware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Super Admins are not bound by location, but may select a session location for scoping.
            if ($user->hasRole('Super Admin')) {
                // keep any chosen session location if present
            } else {
                // For other roles, ensure their location is set in the session
                if ($user->location_id) {
                    session(['location_id' => $user->location_id]);
                } else {
                    // This case should be prevented by our login logic, but as a fallback:
                    if (!app()->runningUnitTests()) {
                        abort(403, 'You are not assigned to a location.');
                    }
                }
            }
        }

        return $next($request);
    }
}

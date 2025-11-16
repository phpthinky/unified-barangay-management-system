<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for guest users
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Allow access if email is verified
        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Allow access to verification routes
        $allowedRoutes = [
            'verification.notice',
            'verification.verify',
            'verification.resend',
            'logout',
        ];

        if ($request->routeIs($allowedRoutes)) {
            return $next($request);
        }

        // Block access to services for unverified users
        if ($user->hasRole('resident')) {
            // Allow access to dashboard but show warning
            if ($request->routeIs('resident.dashboard')) {
                return $next($request);
            }

            // Block all other resident routes
            if ($request->routeIs('resident.*')) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Please verify your email address to access this service.');
            }
        }

        // Allow staff/admin to access without email verification
        if ($user->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-staff', 'barangay-councilor'])) {
            return $next($request);
        }

        return $next($request);
    }
}
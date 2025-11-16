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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is archived
        if ($user->is_archived) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been archived.');
        }

        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is currently inactive.');
        }

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
           // abort(403, 'Insufficient permissions.');
            return redirect()->route('guest.dashboard');

        }
        /* if (!auth()->user()->hasProperRole()) {
        return redirect()->route('guest.dashboard');
         }*/
    
    return $next($request);

        // Update last login timestamp
        $user->updateLastLogin();

        return $next($request);
    }
}

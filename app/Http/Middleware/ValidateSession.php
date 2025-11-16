<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidateSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionToken = session('user_session_token');
            
            // ✅ Check if session token matches
            if ($user->session_token && $sessionToken !== $user->session_token) {
                
                \Log::warning('Session token mismatch - user logged out', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                Auth::logout();
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('warning', 'You have been logged out because a new session was started on another device.');
            }
        }
        
        return $next($request);
    }
}
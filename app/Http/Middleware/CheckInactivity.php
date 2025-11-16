<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInactivity
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
            
            // ✅ Get last activity time from session
            $lastActivity = session('last_activity_time');
            $now = now()->timestamp;
            
            // ✅ 10 minutes = 600 seconds
            $inactivityLimit = 600;
            
            if ($lastActivity) {
                $inactiveTime = $now - $lastActivity;
                
                // ✅ If inactive for more than 10 minutes
                if ($inactiveTime > $inactivityLimit) {
                    
                    // ✅ Clear session token and logout flags
                    $user->update([
                        'is_logged_in' => false,
                        'session_token' => null,
                        'last_activity_at' => now(),
                    ]);
                    
                    Auth::logout();
                    
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->with('warning', 'You were automatically logged out due to 10 minutes of inactivity.');
                }
            }
            
            // ✅ Update last activity time in session
            session(['last_activity_time' => $now]);
            
            // ✅ Update last_activity_at in database (every 2 minutes to reduce DB writes)
            if (!$lastActivity || ($now - $lastActivity) > 120) {
                $user->update(['last_activity_at' => now()]);
            }
        }
        
        return $next($request);
    }
}
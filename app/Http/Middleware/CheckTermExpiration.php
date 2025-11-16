<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTermExpiration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // ✅ Check if user has barangay roles (officials)
            if ($user->hasAnyRole(['barangay-captain', 'barangay-secretary', 'barangay-treasurer', 'barangay-councilor', 'barangay-staff'])) {
                
                // ✅ Check if term has ended
                if ($user->term_end && now()->greaterThan($user->term_end)) {
                    
                    // ✅ Auto-archive the user
                    if (!$user->is_archived) {
                        $user->update([
                            'is_archived' => true,
                            'is_active' => false,
                            'archived_at' => now(),
                            'archived_by' => null, // System auto-archived
                        ]);
                    }
                    
                    Auth::logout();
                    
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login')
                        ->with('error', 'Your term has expired. Your account has been archived. Please contact the administrator.');
                }
            }
        }
        
        return $next($request);
    }
}
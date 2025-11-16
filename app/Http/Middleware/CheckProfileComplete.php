<?php
// app/Http/Middleware/CheckProfileComplete.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    // app/Http/Middleware/CheckProfileComplete.php
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Only check for residents
        if ($user && $user->hasRole('resident')) {
            if (!$user->profile || !$user->profile->isComplete()) {
                $allowed = ['profile.edit', 'profile.update', 'logout'];
                
                if (!in_array($request->route()->getName(), $allowed)) {
                    return redirect()->route('profile.edit')
                        ->with('warning', 'Please complete your profile first');
                }
            }
        }
        
        return $next($request);
    }
}
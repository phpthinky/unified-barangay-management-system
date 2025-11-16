<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BarangayInhabitant;

class CheckDocumentEligibility
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has RBI record
        $inhabitant = BarangayInhabitant::where('user_id', $user->id)
                                       ->where('barangay_id', $user->barangay_id)
                                       ->first();
        
        if (!$inhabitant) {
            return redirect()->back()->with('error', 'You are not registered in the Barangay Registry. Please visit the barangay hall to register first.');
        }
        
        // Store inhabitant in request for later use
        $request->merge(['inhabitant' => $inhabitant]);
        
        return $next($request);
    }
}
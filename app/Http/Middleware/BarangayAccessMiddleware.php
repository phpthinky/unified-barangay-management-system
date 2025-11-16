<?php

// App\Http\Middleware\BarangayAccessMiddleware.php (Additional middleware for specific barangay access)
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarangayAccessMiddleware
{
    /**
     * Handle barangay-specific access control
     * Usage: Route::middleware('barangay.access:123')->...
     */
    public function handle(Request $request, Closure $next, $barangayId = null): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // If no specific barangay ID provided, get from route parameter
        if (!$barangayId) {
            $barangayId = $request->route('barangay') 
                ?? $request->route('barangay_id') 
                ?? $request->get('barangay_id');
        }

        // Check if user can access this barangay
        if ($barangayId && !$user->canAccessBarangay($barangayId)) {
            abort(403, 'Access denied. You do not have permission to access this barangay.');
        }

        return $next($request);
    }
}
<?php

// App\Http\Middleware\BarangayScope.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarangayScope
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has barangay-level access
        if (!$user->isBarangayStaff()) {
            abort(403, 'Access denied. Barangay-level access required.');
        }

        // Add barangay scoping to the request for use in controllers
        // This ensures users can only access data from their assigned barangay
        if ($user->barangay_id) {
            $request->merge(['scoped_barangay_id' => $user->barangay_id]);
        }

        return $next($request);
    }
}


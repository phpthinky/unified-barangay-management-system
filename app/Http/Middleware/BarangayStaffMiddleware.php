<?php
// App\Http\Middleware\BarangayStaffMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarangayStaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isBarangayStaff()) {
            abort(403, 'Access denied. Barangay staff role required.');
        }

        return $next($request);
    }
}

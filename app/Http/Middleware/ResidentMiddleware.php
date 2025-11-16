<?php
// App\Http\Middleware\ResidentMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResidentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isResident()) {
            abort(403, 'Access denied. Resident role required.');
        }

        return $next($request);
    }
}


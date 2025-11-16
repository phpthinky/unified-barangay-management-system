<?php
// App\Http\Middleware\LuponMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LuponMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isLupon()) {
            abort(403, 'Access denied. Lupon member role required.');
        }

        return $next($request);
    }
}

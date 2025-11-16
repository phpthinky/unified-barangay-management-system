<?php
// App\Http\Middleware\AbcPresidentMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AbcPresidentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isAbcPresident()) {
            abort(403, 'Access denied. ABC President role required.');
        }

        return $next($request);
    }
}

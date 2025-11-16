<?php

// App\Http\Middleware\MunicipalityAdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MunicipalityAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isMunicipalityAdmin()) {
            abort(403, 'Access denied. Municipality Admin role required.');
        }

        return $next($request);
    }
}

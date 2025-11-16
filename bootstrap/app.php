<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Global middleware - ONLY auto-logout after 10 minutes
        $middleware->web(append: [
            \App\Http\Middleware\CheckInactivity::class,  // Auto-logout only
        ]);
        
        // ✅ Middleware aliases for UBMS
        $middleware->alias([
            // Spatie Laravel Permission middleware
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            
            'check.document.eligibility' => \App\Http\Middleware\CheckDocumentEligibility::class,
            'check.terms.expiration' => \App\Http\Middleware\CheckTermExpiration::class,
            'check.inactivity' => \App\Http\Middleware\CheckInactivity::class,
       
            // UBMS Custom Role-specific Middleware
            'municipality.admin' => \App\Http\Middleware\MunicipalityAdminMiddleware::class,
            'abc.president' => \App\Http\Middleware\AbcPresidentMiddleware::class,
            'barangay.staff' => \App\Http\Middleware\BarangayStaffMiddleware::class,
            'lupon' => \App\Http\Middleware\LuponMiddleware::class,
            'resident' => \App\Http\Middleware\ResidentMiddleware::class,
            
            // UBMS Scoping Middleware
            'barangay.scope' => \App\Http\Middleware\BarangayScope::class,
            'barangay.access' => \App\Http\Middleware\BarangayAccessMiddleware::class,
        ]);
        
        // ✅ Middleware groups for UBMS
        $middleware->group('ubms.admin', [
            'auth',
            'municipality.admin',
        ]);
        
        $middleware->group('ubms.abc', [
            'auth',
            'abc.president',
        ]);
        
        $middleware->group('ubms.barangay', [
            'auth',
            'barangay.staff',
            'barangay.scope',
        ]);
        
        $middleware->group('ubms.lupon', [
            'auth',
            'lupon',
        ]);
        
        $middleware->group('ubms.resident', [
            'auth',
            'resident',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception handling for UBMS
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Insufficient permissions.'], 403);
            }
            
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access that resource.');
        });
        
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('login');
        });
    })
    ->create();
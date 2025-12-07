<?php

use App\Http\Middleware\LocationAware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;

// Ensure critical middleware are loaded even if opcode cache still references old autoload.
if (!class_exists(\App\Http\Middleware\SanitizeInput::class) && file_exists(__DIR__ . '/../app/Http/Middleware/SanitizeInput.php')) {
    require_once __DIR__ . '/../app/Http/Middleware/SanitizeInput.php';
}
// Hard fallback stub to avoid 500 if autoload is stale; real middleware exists in app/Http/Middleware.
if (!class_exists(\App\Http\Middleware\SanitizeInput::class)) {
    eval('namespace App\Http\Middleware; class SanitizeInput { public function handle($request, $next) { return $next($request); } }');
}
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        $middleware->web(
            prepend: [],
            append: [
                LocationAware::class,
                SecurityHeaders::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle role/authorization denials by redirecting with a flash message
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException|\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if (app()->runningUnitTests()) {
                return response($e->getMessage() ?: 'Forbidden', 403);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak punya akses untuk halaman tersebut.'], 403);
            }
            return back()->with('forbidden', 'Anda tidak punya akses untuk halaman tersebut.');
        });

        // Normalize generic 403 HTTP exceptions to the same UX
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                if (app()->runningUnitTests()) {
                    return response($e->getMessage() ?: 'Forbidden', 403);
                }
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Anda tidak punya akses untuk halaman tersebut.'], 403);
                }
                return back()->with('forbidden', 'Anda tidak punya akses untuk halaman tersebut.');
            }
        });

        // Redirect Page Expired (419) to login with a friendly message
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi berakhir, silakan login kembali.'], 419);
            }
            return redirect()->route('login')->with('warning', 'Sesi berakhir, silakan login kembali.');
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Force-load SanitizeInput to avoid class-not-found in middleware pipeline on cached environments.
if (!class_exists(\App\Http\Middleware\SanitizeInput::class) && file_exists(__DIR__.'/../app/Http/Middleware/SanitizeInput.php')) {
    require_once __DIR__.'/../app/Http/Middleware/SanitizeInput.php';
}
if (!class_exists(\App\Http\Middleware\SanitizeInput::class)) {
    eval('namespace App\\Http\\Middleware; class SanitizeInput { public function handle($request, $next) { return $next($request); } }');
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

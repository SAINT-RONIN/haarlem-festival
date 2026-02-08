<?php

/**
 * Front Controller - Entry point for all HTTP requests.
 *
 * All requests are routed through this file via nginx configuration.
 * Uses FastRoute for routing and dispatches to appropriate controllers.
 */

declare(strict_types=1);

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from .env file (fallback for local development)
// In Docker, environment variables are set directly via docker-compose.yml
$envPath = __DIR__ . '/../../.env';
if (!getenv('DB_HOST') && file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            putenv(trim($line));
        }
    }
}

use App\Controllers\AuthController;
use App\Controllers\CmsAuthController;
use App\Controllers\CmsDashboardController;
use App\Controllers\HomeController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

// Define routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Homepage
    $r->addRoute('GET', '/', [HomeController::class, 'index']);

    // Website Authentication Routes
    $r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    $r->addRoute('GET', '/register', [AuthController::class, 'showRegister']);
    $r->addRoute('POST', '/register', [AuthController::class, 'register']);
    $r->addRoute('GET', '/forgot-password', [AuthController::class, 'showForgotPassword']);
    $r->addRoute('POST', '/forgot-password', [AuthController::class, 'forgotPassword']);
    $r->addRoute('GET', '/reset-password', [AuthController::class, 'showResetPassword']);
    $r->addRoute('POST', '/reset-password', [AuthController::class, 'resetPassword']);

    // CMS Authentication Routes
    $r->addRoute('GET', '/cms/login', [CmsAuthController::class, 'showLogin']);
    $r->addRoute('POST', '/cms/login', [CmsAuthController::class, 'login']);
    $r->addRoute('GET', '/cms/logout', [CmsAuthController::class, 'logout']);

    // CMS Dashboard Routes
    $r->addRoute('GET', '/cms', [CmsDashboardController::class, 'index']);
    $r->addRoute('GET', '/cms/pages', [CmsDashboardController::class, 'pages']);
    $r->addRoute('GET', '/cms/pages/{id:\d+}/edit', [CmsDashboardController::class, 'edit']);
    $r->addRoute('POST', '/cms/pages/{id:\d+}/edit', [CmsDashboardController::class, 'update']);
    $r->addRoute('POST', '/cms/pages/{id:\d+}/upload-image', [CmsDashboardController::class, 'uploadImage']);
});

// Fetch method and URI from request
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch the route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Handle closure routes
        if ($handler instanceof \Closure) {
            $handler(...array_values($vars));
            break;
        }

        // Handle controller routes
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->$method(...array_values($vars));
        break;
}

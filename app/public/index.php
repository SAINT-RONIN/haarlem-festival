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

use App\Controllers\HomeController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

// Define routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Homepage
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
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

        // Instantiate controller and call method
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        $controller->$method(...array_values($vars));
        break;
}

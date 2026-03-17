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
use App\Controllers\CheckoutController;
use App\Controllers\CmsAuthController;
use App\Controllers\CmsDashboardController;
use App\Controllers\CmsEventsController;
use App\Controllers\HistoryController;
use App\Controllers\HomeController;
use App\Controllers\JazzController;
use App\Controllers\ProgramController;
use App\Controllers\RestaurantController;
use App\Controllers\StorytellingController;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

// Define routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Homepage
    $r->addRoute('GET', '/', [HomeController::class, 'index']);


    // History pages
    $r->addRoute('GET', '/history', [HistoryController::class, 'index']);
    $r->addRoute('GET', '/history/{name}', [HistoryController::class, 'location']);

    // Jazz page
    $r->addRoute('GET', '/jazz', [JazzController::class, 'index']);
    $r->addRoute('GET', '/jazz/gumbo-kings', [JazzController::class, 'gumboKings']);
    $r->addRoute('GET', '/jazz/ntjam-rosie', [JazzController::class, 'ntjamRosie']);

    // Storytelling page
    $r->addRoute('GET', '/storytelling', [StorytellingController::class, 'index']);
    $r->addRoute('GET', '/storytelling/{id:\d+}', [StorytellingController::class, 'detail']);

    // Restaurant page
    $r->addRoute('GET', '/restaurant', [RestaurantController::class, 'index']);
    $r->addRoute('GET', '/restaurant/{id:\d+}', [RestaurantController::class, 'detail']);

    // My Program (cart) Routes
    $r->addRoute('GET', '/my-program', [ProgramController::class, 'index']);
    $r->addRoute('POST', '/api/program/add', [ProgramController::class, 'add']);
    $r->addRoute('POST', '/api/program/update-quantity', [ProgramController::class, 'updateQuantity']);
    $r->addRoute('POST', '/api/program/update-donation', [ProgramController::class, 'updateDonation']);
    $r->addRoute('POST', '/api/program/remove', [ProgramController::class, 'remove']);
    $r->addRoute('POST', '/api/program/clear', [ProgramController::class, 'clear']);

    // Checkout Routes
    $r->addRoute('GET', '/checkout', [CheckoutController::class, 'index']);


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


    // CMS Events Routes
    $r->addRoute('GET', '/cms/events', [CmsEventsController::class, 'index']);
    $r->addRoute('GET', '/cms/events/create', [CmsEventsController::class, 'create']);
    $r->addRoute('POST', '/cms/events', [CmsEventsController::class, 'store']);
    $r->addRoute('GET', '/cms/events/{id:\d+}/edit', [CmsEventsController::class, 'edit']);
    $r->addRoute('POST', '/cms/events/{id:\d+}/edit', [CmsEventsController::class, 'update']);
    $r->addRoute('POST', '/cms/events/{id:\d+}/delete', [CmsEventsController::class, 'delete']);
    $r->addRoute('POST', '/cms/events/{eventId:\d+}/sessions', [CmsEventsController::class, 'createSession']);
    $r->addRoute('POST', '/cms/sessions/{id:\d+}', [CmsEventsController::class, 'updateSession']);
    $r->addRoute('POST', '/cms/sessions/{id:\d+}/delete', [CmsEventsController::class, 'deleteSession']);
    $r->addRoute('POST', '/cms/sessions/{id:\d+}/labels', [CmsEventsController::class, 'addLabel']);
    $r->addRoute('POST', '/cms/labels/{id:\d+}/delete', [CmsEventsController::class, 'deleteLabel']);
    $r->addRoute('POST', '/cms/sessions/{id:\d+}/price', [CmsEventsController::class, 'setPrice']);
    $r->addRoute('POST', '/cms/venues', [CmsEventsController::class, 'createVenue']);
    $r->addRoute('GET', '/cms/schedule-days', [CmsEventsController::class, 'scheduleDays']);
    $r->addRoute('POST', '/cms/schedule-days/toggle', [CmsEventsController::class, 'toggleScheduleDay']);

    // Slug-aware routes (preferred)
    $r->addRoute('GET', '/cms/pages/{id:\d+}/{slug:[a-z0-9-]+}/edit', [CmsDashboardController::class, 'edit']);
    $r->addRoute('POST', '/cms/pages/{id:\d+}/{slug:[a-z0-9-]+}/edit', [CmsDashboardController::class, 'update']);
    $r->addRoute('POST', '/cms/pages/{id:\d+}/{slug:[a-z0-9-]+}/upload-image', [CmsDashboardController::class, 'uploadImage']);

    // Legacy routes (id-only)
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
        require __DIR__ . '/../src/Views/pages/errors/404.php';
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

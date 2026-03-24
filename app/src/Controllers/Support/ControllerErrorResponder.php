<?php

declare(strict_types=1);

namespace App\Controllers\Support;

/**
 * Centralized error-response helper for controllers. Detects whether the client
 * expects HTML or JSON and renders the appropriate error format (500 page vs JSON body).
 */
final class ControllerErrorResponder
{
    /** Routes the error to either a JSON response or a rendered 500 page based on the Accept header. */
    public static function respond(\Throwable $error, int $statusCode = 500): void
    {
        if (self::expectsJson()) {
            self::respondJson($error, $statusCode);
            return;
        }

        $errorMessage = $error->getMessage();
        http_response_code($statusCode);
        require __DIR__ . '/../../Views/pages/errors/500.php';
    }

    /** Sends an error as a JSON object, flushing any buffered output first to keep the response clean. */
    public static function respondJson(\Throwable $error, int $statusCode = 500): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        try {
            echo json_encode([
                'success' => false,
                'error' => $error->getMessage(),
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            http_response_code(500);
            echo '{"success":false,"error":"Unexpected server error."}';
        }

        exit;
    }

    /** Checks Accept header and X-Requested-With to determine if the client expects a JSON response. */
    private static function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

}

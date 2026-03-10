<?php

declare(strict_types=1);

namespace App\Controllers\Support;

final class ControllerErrorResponder
{
    public static function respond(\Throwable $error, int $statusCode = 500): void
    {
        if (self::expectsJson()) {
            self::respondJson($error, $statusCode);
            return;
        }

        self::startSessionIfNeeded();
        $_SESSION['error'] = $error->getMessage();
        http_response_code($statusCode);
        require __DIR__ . '/../../Views/pages/errors/500.php';
    }

    public static function respondJson(\Throwable $error, int $statusCode = 500): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $error->getMessage(),
        ]);
    }

    private static function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    private static function startSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\Interfaces\ISessionService;

/**
 * Base controller for CMS entity controllers.
 *
 * Provides shared CSRF validation and flash-redirect helpers
 * so individual CMS controllers don't duplicate this logic.
 */
abstract class CmsBaseController
{
    public function __construct(
        protected readonly ISessionService $sessionService,
    ) {
    }

    protected function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $_POST['_csrf'] ?? null)) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    protected function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        header('Location: ' . $url);
        exit;
    }
}

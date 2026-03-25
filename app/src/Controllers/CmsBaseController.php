<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\ISessionService;

/**
 * Base controller for CMS entity controllers.
 *
 * Provides the admin authentication guard (called automatically in the constructor),
 * shared CSRF validation, and flash-redirect helpers so individual CMS controllers
 * don't duplicate this logic.
 */
abstract class CmsBaseController extends BaseController
{
    public function __construct(
        protected readonly ISessionService $sessionService,
    ) {
        $this->requireAdmin();
    }

    /**
     * Resolves the admin guard for all CMS controllers.
     * Called automatically in the constructor so child controllers
     * do not need to repeat the check.
     */
    private function requireAdmin(): void
    {
        try {
            $this->sessionService->start();

            if (!$this->sessionService->isLoggedIn() || !$this->sessionService->isAdmin()) {
                header('Location: /cms/login');
                exit;
            }
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Verifies the CSRF token from POST data; redirects with an error flash if invalid.
     */
    protected function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $_POST['_csrf'] ?? null)) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /**
     * Sets a flash message on the session and issues a redirect to the given URL.
     */
    protected function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirects with a comma-separated error flash from a ValidationException.
     */
    protected function redirectWithValidationErrors(ValidationException $error, string $url): void
    {
        $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', $url);
    }
}

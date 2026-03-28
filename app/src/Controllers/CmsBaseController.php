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
    public function __construct(ISessionService $sessionService)
    {
        parent::__construct($sessionService);
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
                $this->redirectAndExit('/cms/login');
            }
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * @param callable(): void $action
     */
    protected function handleCmsPageRequest(callable $action): void
    {
        $this->handlePageRequest($action);
    }

    /**
     * @param callable(): void $action
     * @param string|callable(): string $validationRedirectUrl
     */
    protected function handleCmsValidationRequest(callable $action, string|callable $validationRedirectUrl): void
    {
        try {
            $action();
        } catch (ValidationException $error) {
            $redirectUrl = is_callable($validationRedirectUrl) ? $validationRedirectUrl() : $validationRedirectUrl;
            $this->redirectWithValidationErrors($error, $redirectUrl);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * @param callable(): void $action
     */
    protected function handleCmsJsonRequest(callable $action): void
    {
        $this->handleJsonRequest($action, [ValidationException::class]);
    }

    /**
     * Verifies the CSRF token from POST data; redirects with an error flash if invalid.
     */
    protected function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $this->readStringPostParam('_csrf'))) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            $this->redirectAndExit($redirectUrl);
        }
    }

    /**
     * Sets a flash message on the session and issues a redirect to the given URL.
     */
    protected function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        $this->redirectAndExit($url);
    }

    /**
     * Redirects with a comma-separated error flash from a ValidationException.
     */
    protected function redirectWithValidationErrors(ValidationException $error, string $url): void
    {
        $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', $url);
    }
}

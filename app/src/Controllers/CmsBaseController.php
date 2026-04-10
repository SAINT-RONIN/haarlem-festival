<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\ValidationException;
use App\Services\Interfaces\ISessionService;

/**
 * Base controller for CMS entity controllers.
 *
 * Provides admin authentication guard (auto-called in constructor), CSRF validation,
 * and flash-redirect helpers.
 */
abstract class CmsBaseController extends BaseController
{
    public function __construct(ISessionService $sessionService)
    {
        parent::__construct($sessionService);
        $this->requireAdmin();
    }

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

    protected function handleCmsPageRequest(callable $action): void
    {
        $this->handlePageRequest($action);
    }

    /** @param string|callable(): string $validationRedirectUrl */
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

    protected function handleCmsJsonRequest(callable $action): void
    {
        $this->handleJsonRequest($action, [ValidationException::class]);
    }

    /** Verifies the CSRF token from POST data; redirects with an error flash if invalid. */
    protected function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $this->readStringPostParam('_csrf'))) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            $this->redirectAndExit($redirectUrl);
        }
    }

    protected function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        $this->redirectAndExit($url);
    }

    protected function redirectWithValidationErrors(ValidationException $error, string $url): void
    {
        $this->redirectWithFlash(implode(', ', $error->getErrors()), 'error', $url);
    }
}

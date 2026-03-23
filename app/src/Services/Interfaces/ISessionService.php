<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Session management service.
 */
interface ISessionService
{
    /**
     * Starts the session if not already started.
     */
    public function start(): void;

    /**
     * Logs in a user by storing their ID and role in session.
     */
    public function login(int $userId, int $roleId): void;

    /**
     * Logs out the current user by destroying the session.
     */
    public function logout(): void;

    /**
     * Checks if a user is currently logged in.
     */
    public function isLoggedIn(): bool;

    /**
     * Checks if the current user is an administrator.
     */
    public function isAdmin(): bool;

    /**
     * Gets the current user's ID.
     */
    public function getUserId(): ?int;

    /**
     * Sets a session key to a scalar/array value.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Gets a session value by key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Stores a flash message available for the next request.
     */
    public function setFlash(string $key, string $message): void;

    /**
     * Retrieves and removes a flash message.
     */
    public function consumeFlash(string $key): ?string;

    /**
     * Generates (or returns existing) CSRF token for a form scope.
     */
    public function getCsrfToken(string $scope): string;

    /**
     * Validates a CSRF token for a form scope.
     */
    public function isValidCsrfToken(string $scope, ?string $token): bool;

    /**
     * Returns the active session ID.
     *
     * @throws \RuntimeException when session has not been started
     */
    public function getSessionId(): string;
}

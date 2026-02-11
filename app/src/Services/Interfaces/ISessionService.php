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
     * Gets the current user's role ID.
     */
    public function getRoleId(): ?int;
}

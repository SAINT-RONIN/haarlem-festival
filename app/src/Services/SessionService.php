<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service for managing PHP sessions.
 *
 * Handles session lifecycle, user login state, and role-based access checks.
 * All session operations are centralized here for consistency and security.
 */
class SessionService
{
    private const USER_ID_KEY = 'user_id';
    private const ROLE_ID_KEY = 'role_id';
    private const ADMIN_ROLE_ID = 3;

    /**
     * Starts the session if not already started.
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Logs in a user by storing their ID and role in session.
     * Regenerates session ID to prevent session fixation attacks.
     */
    public function login(int $userId, int $roleId): void
    {
        $this->start();

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION[self::USER_ID_KEY] = $userId;
        $_SESSION[self::ROLE_ID_KEY] = $roleId;
    }

    /**
     * Logs out the current user by destroying the session.
     */
    public function logout(): void
    {
        $this->start();

        // Clear all session data
        $_SESSION = [];

        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Checks if a user is currently logged in.
     */
    public function isLoggedIn(): bool
    {
        $this->start();
        return isset($_SESSION[self::USER_ID_KEY]);
    }

    /**
     * Checks if the current user is an administrator.
     */
    public function isAdmin(): bool
    {
        $this->start();
        return isset($_SESSION[self::ROLE_ID_KEY])
            && $_SESSION[self::ROLE_ID_KEY] === self::ADMIN_ROLE_ID;
    }

    /**
     * Gets the current user's ID, or null if not logged in.
     */
    public function getUserId(): ?int
    {
        $this->start();
        return $_SESSION[self::USER_ID_KEY] ?? null;
    }

    /**
     * Gets the current user's role ID, or null if not logged in.
     */
    public function getRoleId(): ?int
    {
        $this->start();
        return $_SESSION[self::ROLE_ID_KEY] ?? null;
    }
}


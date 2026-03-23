<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoleId;
use App\Services\Interfaces\ISessionService;

/**
 * Service for managing PHP sessions.
 *
 * Handles session lifecycle, user login state, and role-based access checks.
 * All session operations are centralized here for consistency and security.
 */
class SessionService implements ISessionService
{
    private const USER_ID_KEY = 'user_id';
    private const ROLE_ID_KEY = 'role_id';
    private const FLASH_KEY = '_flash';
    private const CSRF_KEY = '_csrf_tokens';

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
            && $_SESSION[self::ROLE_ID_KEY] === UserRoleId::Administrator->value;
    }

    /**
     * Gets the current user's ID, or null if not logged in.
     */
    public function getUserId(): ?int
    {
        $this->start();
        return $_SESSION[self::USER_ID_KEY] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Stores a flash message that survives exactly one subsequent read via consumeFlash().
     */
    public function setFlash(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    /**
     * Reads and removes a flash message in one step. Returns null if the key does not exist.
     */
    public function consumeFlash(string $key): mixed
    {
        $this->start();

        $value = $_SESSION[self::FLASH_KEY][$key] ?? null;
        if (isset($_SESSION[self::FLASH_KEY][$key])) {
            unset($_SESSION[self::FLASH_KEY][$key]);
        }

        if (isset($_SESSION[self::FLASH_KEY]) && $_SESSION[self::FLASH_KEY] === []) {
            unset($_SESSION[self::FLASH_KEY]);
        }

        return $value;
    }

    /**
     * Returns an existing CSRF token for the given scope, or generates and stores a new one.
     * Tokens are scoped so different forms cannot reuse each other's tokens.
     */
    public function getCsrfToken(string $scope): string
    {
        $this->start();

        $existing = $_SESSION[self::CSRF_KEY][$scope] ?? null;
        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::CSRF_KEY][$scope] = $token;
        return $token;
    }

    /**
     * Validates a submitted CSRF token against the stored token for the given scope.
     * Uses timing-safe comparison to prevent timing attacks.
     */
    public function isValidCsrfToken(string $scope, ?string $token): bool
    {
        $this->start();

        if (!is_string($token) || $token === '') {
            return false;
        }

        $expected = $_SESSION[self::CSRF_KEY][$scope] ?? null;
        return is_string($expected) && hash_equals($expected, $token);
    }

    /**
     * Returns the current PHP session ID. Used as a key to associate anonymous programs with a browser.
     *
     * @throws \RuntimeException When called before a session has been started
     */
    public function getSessionId(): string
    {
        $this->start();

        $sessionId = session_id();
        if ($sessionId === '') {
            throw new \RuntimeException('Session has not been initialized for this request.');
        }

        return $sessionId;
    }
}

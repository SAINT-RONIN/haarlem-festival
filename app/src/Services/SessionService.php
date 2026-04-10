<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoleId;
use App\Services\Interfaces\ISessionService;

// Controllers and middleware use this instead of touching $_SESSION directly
// so session key names stay consistent and security logic stays in one place.
class SessionService implements ISessionService
{
    private const USER_ID_KEY = 'user_id';
    private const ROLE_ID_KEY = 'role_id';
    private const FLASH_KEY = '_flash';
    private const CSRF_KEY = '_csrf_tokens';

    private const SESSION_LIFETIME = 60 * 60 * 8; // 8 hours

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            ini_set('session.gc_maxlifetime', (string) self::SESSION_LIFETIME);
            session_start();
        }
    }

    public function login(int $userId, int $roleId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User ID must be a positive integer.');
        }

        if (UserRoleId::tryFrom($roleId) === null) {
            throw new \InvalidArgumentException('Invalid role ID.');
        }

        $this->start();

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION[self::USER_ID_KEY] = $userId;
        $_SESSION[self::ROLE_ID_KEY] = $roleId;
    }

    public function logout(): void
    {
        $this->start();

        $_SESSION = [];

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

        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        $this->start();
        return isset($_SESSION[self::USER_ID_KEY]);
    }

    public function isAdmin(): bool
    {
        $this->start();
        return isset($_SESSION[self::ROLE_ID_KEY])
            && $_SESSION[self::ROLE_ID_KEY] === UserRoleId::Administrator->value;
    }

    public function isEmployeeOrAdmin(): bool
    {
        $this->start();

        $roleId = $_SESSION[self::ROLE_ID_KEY] ?? null;

        return $roleId === UserRoleId::Employee->value
            || $roleId === UserRoleId::Administrator->value;
    }

    public function isEmployee(): bool
    {
        $this->start();
        return isset($_SESSION[self::ROLE_ID_KEY])
            && $_SESSION[self::ROLE_ID_KEY] === UserRoleId::Employee->value;
    }

    public function getUserId(): ?int
    {
        $this->start();
        return $_SESSION[self::USER_ID_KEY] ?? null;
    }

    public function getRoleId(): ?int
    {
        $this->start();
        return $_SESSION[self::ROLE_ID_KEY] ?? null;
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

    public function setFlash(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[self::FLASH_KEY][$key] = $value;
    }

    public function consumeFlash(string $key): mixed
    {
        $this->start();

        $value = $_SESSION[self::FLASH_KEY][$key] ?? null;
        unset($_SESSION[self::FLASH_KEY][$key]);

        // Remove the flash container entirely when empty to keep $_SESSION clean
        if (($_SESSION[self::FLASH_KEY] ?? []) === []) {
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

    /** Uses timing-safe comparison to prevent timing attacks. */
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

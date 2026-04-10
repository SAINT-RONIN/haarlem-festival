<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Session;

/**
 * Encapsulates the session state needed by controllers that manage
 * cart or program operations: the anonymous session key, the optional
 * logged-in user ID, and a convenience boolean for auth checks.
 */
final readonly class SessionContext
{
    public function __construct(
        public string $sessionKey,
        public ?int $userId,
        public bool $isLoggedIn,
    ) {}
}

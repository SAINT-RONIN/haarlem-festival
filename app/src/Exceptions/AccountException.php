<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when account management operations fail (profile update, email confirmation, etc.).
 *
 * Wraps underlying errors in a domain-specific exception for clean error handling
 * in the AccountService and AccountController layers.
 */
final class AccountException extends \Exception
{
    /**
     * @param string $message Error message describing what went wrong
     * @param int $code Optional error code (default: 0)
     * @param \Throwable|null $previous Optional previous exception for error chaining
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}



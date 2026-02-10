<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when validation fails.
 */
class ValidationException extends \Exception
{
    /** @var array<string> */
    private array $errors;

    /**
     * @param array<string>|string $errors Validation error messages
     */
    public function __construct(array|string $errors = 'Validation failed', int $code = 0, ?\Throwable $previous = null)
    {
        if (is_array($errors)) {
            $this->errors = $errors;
            $message = implode(', ', $errors);
        } else {
            $this->errors = [$errors];
            $message = $errors;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns all validation error messages.
     *
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}


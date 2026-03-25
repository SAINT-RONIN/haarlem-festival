<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when the request body is missing or contains invalid JSON.
 */
class JsonBodyParseException extends AppException
{
}

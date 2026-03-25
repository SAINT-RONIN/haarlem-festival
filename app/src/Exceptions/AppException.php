<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Base exception for all application-level exceptions.
 *
 * All custom domain exceptions extend this class so they can be caught
 * uniformly at the controller level while still supporting specific
 * catch blocks for targeted error handling.
 */
class AppException extends \RuntimeException
{
}

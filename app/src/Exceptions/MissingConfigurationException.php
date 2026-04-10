<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when a required environment variable or runtime configuration is missing or invalid.
 */
class MissingConfigurationException extends AppException {}

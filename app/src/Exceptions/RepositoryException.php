<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a repository encounters an unexpected database error.
 *
 * Wraps \PDOException so callers in the service layer receive a
 * domain-level exception instead of a raw PDO failure.
 */
final class RepositoryException extends AppException {}

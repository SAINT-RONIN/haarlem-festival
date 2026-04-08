<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a program write succeeds but the new record cannot be read back.
 */
final class ProgramPersistenceException extends ProgramException {}

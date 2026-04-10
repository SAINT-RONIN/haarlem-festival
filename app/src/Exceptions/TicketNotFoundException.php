<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a ticket code does not match any record in the database.
 */
class TicketNotFoundException extends ScannerException {}

<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a ticket PDF cannot be generated or written.
 */
final class TicketPdfGenerationException extends TicketDeliveryException
{
}

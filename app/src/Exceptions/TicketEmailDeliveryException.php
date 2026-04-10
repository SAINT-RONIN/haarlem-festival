<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when ticket email delivery fails after payment completed.
 */
final class TicketEmailDeliveryException extends TicketDeliveryException {}

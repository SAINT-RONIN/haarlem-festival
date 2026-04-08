<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a ticket QR code cannot be generated.
 */
final class TicketQrCodeException extends TicketDeliveryException {}

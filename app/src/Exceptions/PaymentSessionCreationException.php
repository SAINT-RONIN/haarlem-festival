<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown when creating or retrieving a Stripe checkout session fails.
 */
class PaymentSessionCreationException extends AppException
{
}

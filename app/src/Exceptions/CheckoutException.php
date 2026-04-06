<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception thrown during checkout processing (empty cart, overselling, Stripe failures).
 */
class CheckoutException extends AppException
{
}

<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Thrown when a retry payment attempt fails (order not found, expired, wrong status).
 */
class RetryPaymentException extends CheckoutException
{
}

<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Base exception for invoice-related failures during order fulfillment.
 */
class InvoiceException extends CheckoutException {}

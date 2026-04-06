<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Foreseeable checkout error caused by invalid user input or current cart state.
 */
final class CheckoutInputException extends CheckoutException
{
}

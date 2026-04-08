<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Foreseeable webhook error caused by an invalid Stripe payload or signature.
 */
final class StripeWebhookException extends CheckoutException {}

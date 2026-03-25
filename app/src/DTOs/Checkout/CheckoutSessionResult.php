<?php

declare(strict_types=1);

namespace App\DTOs\Checkout;

/**
 * Result of creating a Stripe checkout session — carries the redirect URL and record IDs.
 */
final readonly class CheckoutSessionResult
{
    public function __construct(
        public string $redirectUrl,
        public int $orderId,
        public int $paymentId,
    ) {
    }
}

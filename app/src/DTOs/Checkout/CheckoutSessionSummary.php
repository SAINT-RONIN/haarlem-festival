<?php

declare(strict_types=1);

namespace App\DTOs\Checkout;

/**
 * Normalized summary of a Stripe checkout session for the confirmation page.
 */
final readonly class CheckoutSessionSummary
{
    public function __construct(
        public string $sessionId,
        public string $paymentStatus,
        public string $status,
        public float $amountTotal,
        public string $currency,
    ) {
    }
}

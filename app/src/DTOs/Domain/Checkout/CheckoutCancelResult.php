<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Checkout;

/**
 * Result of a checkout cancellation — carries the status and affected record IDs.
 */
final readonly class CheckoutCancelResult
{
    public function __construct(
        public string $status,
        public ?int $orderId,
        public ?int $paymentId,
    ) {}
}

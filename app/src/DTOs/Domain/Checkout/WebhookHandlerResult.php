<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Checkout;

/**
 * Result of processing a Stripe webhook event — whether it was processed and identifying info.
 */
final readonly class WebhookHandlerResult
{
    public function __construct(
        public bool $processed,
        public string $eventId,
        public string $eventType,
    ) {
    }
}

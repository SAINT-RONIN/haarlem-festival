<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Checkout\WebhookHandlerResult;

/**
 * Processes incoming Stripe webhook events and transitions order/payment statuses.
 */
interface IStripeWebhookHandler
{
    /**
     * Verifies the webhook signature, ensures idempotency, and transitions
     * order/payment statuses based on the Stripe event type.
     *
     * @throws \App\Exceptions\CheckoutException When webhook processing fails
     */
    public function handleWebhook(string $payload, ?string $signatureHeader): WebhookHandlerResult;
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces;

/**
 * Contract for a payment gateway adapter that handles Stripe Checkout Sessions
 * and webhook event verification. Implementations return plain arrays so the
 * rest of the application never depends on Stripe SDK types.
 */
interface IStripeService
{
    /**
     * Creates a hosted Checkout Session on Stripe and returns its payload.
     *
     * @param array<string,mixed> $params Stripe-native session creation parameters
     * @return array<string,mixed> Session payload including 'id' and 'url'
     * @throws \RuntimeException When the Stripe API rejects the request
     */
    public function createCheckoutSession(array $params): array;

    /**
     * Retrieves an existing Checkout Session from Stripe by its ID.
     *
     * @return array<string,mixed> Session payload with expanded payment_intent
     * @throws \InvalidArgumentException When sessionId is blank
     * @throws \RuntimeException When the Stripe API call fails
     */
    public function retrieveCheckoutSession(string $sessionId): array;

    /**
     * Verifies a webhook signature and parses the raw payload into an event array.
     *
     * @param int $toleranceSeconds Maximum event age before rejection (replay-attack guard)
     * @return array<string,mixed> Parsed event with 'id', 'type', and 'data.object'
     * @throws \RuntimeException When the webhook secret is not configured
     * @throws \InvalidArgumentException When signature or payload is invalid
     */
    public function constructWebhookEvent(string $payload, ?string $signatureHeader, int $toleranceSeconds = 300): array;
}

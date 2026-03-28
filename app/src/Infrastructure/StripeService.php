<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Exceptions\StripeNotConfiguredException;
use App\Infrastructure\Interfaces\IStripeService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Thin adapter around the Stripe PHP SDK for payment processing.
 *
 * Keeps Stripe SDK usage localized and returns plain arrays so the rest of the
 * application never depends on Stripe SDK types directly.
 */
final class StripeService implements IStripeService
{
    private StripeClient $client;
    private string $webhookSecret;

    public function __construct(
        #[\SensitiveParameter] string $secretKey,
        #[\SensitiveParameter] string $webhookSecret,
    ) {
        $normalizedSecretKey = trim($secretKey);
        $normalizedWebhookSecret = trim($webhookSecret);

        if ($normalizedSecretKey === '') {
            throw new StripeNotConfiguredException('Stripe secret key is not configured.');
        }

        $this->client = new StripeClient($normalizedSecretKey);
        $this->webhookSecret = $normalizedWebhookSecret;

        // Pinning API version and retries keeps Stripe behavior predictable across upgrades.
        Stripe::setApiKey($normalizedSecretKey);
        Stripe::setMaxNetworkRetries(2);
    }

    /**
     * Creates a Stripe Checkout Session and returns its full payload as an array.
     *
     * @param array<string,mixed> $params Stripe-native session parameters (mode, line_items, success_url, etc.)
     * @return array<string,mixed> The created session payload including 'id' and 'url' keys
     */
    public function createCheckoutSession(array $params): array
    {
        return Session::create($params)->toArray();
    }

    /**
     * Fetches an existing Checkout Session from Stripe, expanding the payment_intent
     * so the caller gets payment status without a second API call.
     *
     * @return array<string,mixed>
     * @throws \InvalidArgumentException When sessionId is blank
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        if ($sessionId === '') {
            throw new \InvalidArgumentException('Missing Stripe session id.');
        }

        $session = $this->client->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent'],
        ]);

        return $session->toArray();
    }

    /**
     * Verifies the Stripe-Signature header and parses the webhook payload into an event array.
     *
     * @param int $toleranceSeconds Maximum age of the event before rejecting it (guards against replay attacks)
     * @return array<string,mixed> Parsed event with 'id', 'type', and 'data.object' keys
     * @throws \InvalidArgumentException When the signature is missing, invalid, or the payload is malformed
     */
    public function constructWebhookEvent(string $payload, ?string $signatureHeader, int $toleranceSeconds = 300): array
    {
        if ($this->webhookSecret === '') {
            throw new StripeNotConfiguredException('Stripe webhook secret is not configured.');
        }

        if (!is_string($signatureHeader) || trim($signatureHeader) === '') {
            throw new \InvalidArgumentException('Missing Stripe-Signature header.');
        }

        return Webhook::constructEvent(
            $payload,
            $signatureHeader,
            $this->webhookSecret,
            $toleranceSeconds,
        )->toArray();
    }
}

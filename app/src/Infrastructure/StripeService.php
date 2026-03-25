<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Exceptions\PaymentSessionCreationException;
use App\Exceptions\StripeNotConfiguredException;
use App\Infrastructure\Interfaces\IStripeService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * Thin adapter around the Stripe PHP SDK for payment processing.
 *
 * Wraps Stripe Checkout Sessions and Webhook signature verification,
 * converting SDK exceptions into standard RuntimeException/InvalidArgumentException.
 * All responses are returned as plain arrays so callers never depend on Stripe SDK types.
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
     * @throws \RuntimeException When the Stripe API rejects the request
     */
    public function createCheckoutSession(array $params): array
    {
        try {
            return Session::create($params)->toArray();
        } catch (ApiErrorException $error) {
            throw new PaymentSessionCreationException('Stripe checkout session creation failed: ' . $error->getMessage(), 0, $error);
        }
    }

    /**
     * Fetches an existing Checkout Session from Stripe, expanding the payment_intent
     * so the caller gets payment status without a second API call.
     *
     * @return array<string,mixed>
     * @throws \InvalidArgumentException When sessionId is blank
     * @throws \RuntimeException When the Stripe API call fails
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        if ($sessionId === '') {
            throw new \InvalidArgumentException('Missing Stripe session id.');
        }

        try {
            $session = $this->client->checkout->sessions->retrieve($sessionId, [
                'expand' => ['payment_intent'],
            ]);

            return $session->toArray();
        } catch (ApiErrorException $error) {
            throw new PaymentSessionCreationException('Stripe checkout session retrieval failed: ' . $error->getMessage(), 0, $error);
        }
    }

    /**
     * Verifies the Stripe-Signature header and parses the webhook payload into an event array.
     *
     * @param int $toleranceSeconds Maximum age of the event before rejecting it (guards against replay attacks)
     * @return array<string,mixed> Parsed event with 'id', 'type', and 'data.object' keys
     * @throws \RuntimeException When the webhook secret is not configured
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

        try {
            return Webhook::constructEvent(
                $payload,
                $signatureHeader,
                $this->webhookSecret,
                $toleranceSeconds,
            )->toArray();
        } catch (SignatureVerificationException $error) {
            throw new \InvalidArgumentException('Invalid Stripe webhook signature: ' . $error->getMessage(), 0, $error);
        } catch (\UnexpectedValueException $error) {
            throw new \InvalidArgumentException('Invalid Stripe webhook payload: ' . $error->getMessage(), 0, $error);
        }
    }
}

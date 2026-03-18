<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\Interfaces\IStripeService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

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
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $this->client = new StripeClient($normalizedSecretKey);
        $this->webhookSecret = $normalizedWebhookSecret;

        // Pinning API version and retries keeps Stripe behavior predictable across upgrades.
        Stripe::setApiKey($normalizedSecretKey);
        Stripe::setMaxNetworkRetries(2);
    }

    /**
     * @param array<string,mixed> $params
     * @return array<string,mixed>
     */
    public function createCheckoutSession(array $params): array
    {
        try {
            return Session::create($params)->toArray();
        } catch (ApiErrorException $error) {
            throw new \RuntimeException('Stripe checkout session creation failed: ' . $error->getMessage(), 0, $error);
        }
    }

    /**
     * @return array<string,mixed>
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
            throw new \RuntimeException('Stripe checkout session retrieval failed: ' . $error->getMessage(), 0, $error);
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function constructWebhookEvent(string $payload, ?string $signatureHeader, int $toleranceSeconds = 300): array
    {
        if ($this->webhookSecret === '') {
            throw new \RuntimeException('Stripe webhook secret is not configured.');
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

<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IStripeService
{
    /**
     * @param array<string,mixed> $params
     * @return array<string,mixed>
     */
    public function createCheckoutSession(array $params): array;

    /**
     * @return array<string,mixed>
     */
    public function retrieveCheckoutSession(string $sessionId): array;

    /**
     * @return array<string,mixed>
     */
    public function constructWebhookEvent(string $payload, ?string $signatureHeader, int $toleranceSeconds = 300): array;
}

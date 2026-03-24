<?php

declare(strict_types=1);

namespace App\Http\Requests;

final readonly class StripeWebhookRequest
{
    public function __construct(
        public string $payload,
        public ?string $signatureHeader,
    ) {
    }
}


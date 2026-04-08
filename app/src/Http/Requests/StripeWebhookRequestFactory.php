<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Interfaces\IStripeWebhookRequestFactory;

final class StripeWebhookRequestFactory implements IStripeWebhookRequestFactory
{
    public function createFromGlobals(): StripeWebhookRequest
    {
        $payload = (string) file_get_contents('php://input');
        $payload = trim($payload);

        if ($payload === '') {
            throw new \InvalidArgumentException('Empty webhook payload.');
        }

        $signatureHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;
        if (is_string($signatureHeader)) {
            $signatureHeader = trim($signatureHeader);
            if ($signatureHeader === '') {
                $signatureHeader = null;
            }
        } else {
            $signatureHeader = null;
        }

        return new StripeWebhookRequest($payload, $signatureHeader);
    }
}

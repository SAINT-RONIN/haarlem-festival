<?php

declare(strict_types=1);

namespace App\Http\Requests\Interfaces;

use App\Http\Requests\StripeWebhookRequest;

interface IStripeWebhookRequestFactory
{
    public function createFromGlobals(): StripeWebhookRequest;
}


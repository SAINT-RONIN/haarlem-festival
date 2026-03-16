<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IStripeWebhookEventRepository
{
    public function hasProcessed(string $eventId): bool;

    public function markProcessed(string $eventId, string $eventType): void;
}


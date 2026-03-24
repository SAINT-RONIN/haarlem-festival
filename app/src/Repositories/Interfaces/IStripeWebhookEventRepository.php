<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Defines persistence operations for tracking processed Stripe webhook events (idempotency guard).
 */
interface IStripeWebhookEventRepository
{
    /**
     * Checks whether a Stripe event has already been processed, preventing duplicate handling.
     */
    public function hasProcessed(string $eventId): bool;

    /**
     * Records a Stripe event as processed so future deliveries of the same event are skipped.
     */
    public function markProcessed(string $eventId, string $eventType): void;

    /**
     * Atomically marks a Stripe event as processed. Returns false if already processed.
     * Preferred over hasProcessed() + markProcessed() to avoid race conditions.
     */
    public function markProcessedIfNew(string $eventId, string $eventType): bool;
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IStripeWebhookEventRepository;

/**
 * Idempotency guard for Stripe webhook processing.
 *
 * Stores each processed Stripe event ID in the StripeWebhookEvent table so
 * duplicate webhook deliveries can be detected and skipped.
 */
class StripeWebhookEventRepository extends BaseRepository implements IStripeWebhookEventRepository
{
    /**
     * Returns true if this Stripe event ID has already been handled (prevents duplicate processing).
     */
    public function hasProcessed(string $eventId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM StripeWebhookEvent WHERE StripeEventId = :eventId LIMIT 1',
            ['eventId' => $eventId],
        );

        return (bool)$stmt->fetchColumn();
    }

    /**
     * Records a Stripe event as processed so future webhook retries are skipped.
     */
    public function markProcessed(string $eventId, string $eventType): void
    {
        $this->execute(
            'INSERT INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)',
            ['eventId' => $eventId, 'eventType' => $eventType],
        );
    }

    /**
     * Atomically marks a Stripe event as processed. Returns false if already processed
     * (duplicate delivery). Uses INSERT IGNORE to handle the race condition where
     * concurrent webhook requests pass hasProcessed() simultaneously.
     */
    public function markProcessedIfNew(string $eventId, string $eventType): bool
    {
        $stmt = $this->execute(
            'INSERT IGNORE INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)',
            ['eventId' => $eventId, 'eventType' => $eventType],
        );

        return $stmt->rowCount() > 0;
    }

    /** Removes the idempotency lock when webhook processing fails and needs to be retried later. */
    public function release(string $eventId): void
    {
        $this->execute(
            'DELETE FROM StripeWebhookEvent WHERE StripeEventId = :eventId',
            ['eventId' => $eventId],
        );
    }
}

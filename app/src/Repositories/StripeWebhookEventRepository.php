<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IStripeWebhookEventRepository;

// Idempotency guard for Stripe webhooks. Stores processed event IDs so
// duplicate deliveries can be detected and skipped.
class StripeWebhookEventRepository extends BaseRepository implements IStripeWebhookEventRepository
{
    public function hasProcessed(string $eventId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM StripeWebhookEvent WHERE StripeEventId = :eventId LIMIT 1',
            ['eventId' => $eventId],
        );

        return (bool) $stmt->fetchColumn();
    }

    public function markProcessed(string $eventId, string $eventType): void
    {
        $this->execute(
            'INSERT INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)',
            ['eventId' => $eventId, 'eventType' => $eventType],
        );
    }

    // INSERT IGNORE handles the race where two webhook requests pass hasProcessed() simultaneously.
    // Returns false when the row already existed (duplicate delivery).
    public function markProcessedIfNew(string $eventId, string $eventType): bool
    {
        $stmt = $this->execute(
            'INSERT IGNORE INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)',
            ['eventId' => $eventId, 'eventType' => $eventType],
        );

        return $stmt->rowCount() > 0;
    }

    // Removes the idempotency lock so the event can be retried later.
    public function release(string $eventId): void
    {
        $this->execute(
            'DELETE FROM StripeWebhookEvent WHERE StripeEventId = :eventId',
            ['eventId' => $eventId],
        );
    }
}

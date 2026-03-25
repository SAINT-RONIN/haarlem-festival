<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use PDO;

/**
 * Idempotency guard for Stripe webhook processing.
 *
 * Stores each processed Stripe event ID in the StripeWebhookEvent table so
 * duplicate webhook deliveries can be detected and skipped.
 */
class StripeWebhookEventRepository implements IStripeWebhookEventRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Returns true if this Stripe event ID has already been handled (prevents duplicate processing).
     */
    public function hasProcessed(string $eventId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM StripeWebhookEvent WHERE StripeEventId = :eventId LIMIT 1');
        $stmt->execute(['eventId' => $eventId]);

        return (bool)$stmt->fetchColumn();
    }

    /**
     * Records a Stripe event as processed so future webhook retries are skipped.
     */
    public function markProcessed(string $eventId, string $eventType): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)
        ');

        $stmt->execute([
            'eventId' => $eventId,
            'eventType' => $eventType,
        ]);
    }

    /**
     * Atomically marks a Stripe event as processed. Returns false if already processed
     * (duplicate delivery). Uses INSERT IGNORE to handle the race condition where
     * concurrent webhook requests pass hasProcessed() simultaneously.
     */
    public function markProcessedIfNew(string $eventId, string $eventType): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT IGNORE INTO StripeWebhookEvent (StripeEventId, EventType)
            VALUES (:eventId, :eventType)
        ');

        $stmt->execute([
            'eventId' => $eventId,
            'eventType' => $eventType,
        ]);

        return $stmt->rowCount() > 0;
    }
}

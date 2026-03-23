<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IStripeWebhookEventRepository;
use PDO;

class StripeWebhookEventRepository implements IStripeWebhookEventRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function hasProcessed(string $eventId): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM StripeWebhookEvent WHERE StripeEventId = :eventId LIMIT 1');
        $stmt->execute(['eventId' => $eventId]);

        return (bool)$stmt->fetchColumn();
    }

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
}


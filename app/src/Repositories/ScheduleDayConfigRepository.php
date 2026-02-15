<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
use PDO;

/**
 * Repository for ScheduleDayConfig database operations.
 */
class ScheduleDayConfigRepository implements IScheduleDayConfigRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all schedule day configurations.
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('
            SELECT 
                sdc.ScheduleDayConfigId,
                sdc.EventTypeId,
                sdc.DayOfWeek,
                sdc.IsVisible,
                et.Name AS EventTypeName
            FROM ScheduleDayConfig sdc
            LEFT JOIN EventType et ON sdc.EventTypeId = et.EventTypeId AND sdc.EventTypeId > 0
            ORDER BY sdc.EventTypeId = 0 DESC, sdc.EventTypeId, sdc.DayOfWeek
        ');
        return $stmt->fetchAll();
    }

    /**
     * Gets global settings (EventTypeId = 0).
     */
    public function findGlobalSettings(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DayOfWeek, IsVisible
            FROM ScheduleDayConfig
            WHERE EventTypeId = 0
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Gets settings for a specific event type.
     */
    public function findByEventTypeId(int $eventTypeId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT DayOfWeek, IsVisible
            FROM ScheduleDayConfig
            WHERE EventTypeId = :eventTypeId
        ');
        $stmt->execute(['eventTypeId' => $eventTypeId]);
        return $stmt->fetchAll();
    }

    /**
     * Upserts a visibility setting.
     */
    public function upsert(int $eventTypeId, int $dayOfWeek, bool $isVisible): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO ScheduleDayConfig (EventTypeId, DayOfWeek, IsVisible)
            VALUES (:eventTypeId, :dayOfWeek, :isVisible)
            ON DUPLICATE KEY UPDATE IsVisible = :isVisible2
        ');
        $stmt->execute([
            'eventTypeId' => $eventTypeId,
            'dayOfWeek' => $dayOfWeek,
            'isVisible' => $isVisible ? 1 : 0,
            'isVisible2' => $isVisible ? 1 : 0,
        ]);
    }
}

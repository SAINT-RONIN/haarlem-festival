<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PassType;
use App\Repositories\Interfaces\IPassTypeRepository;
use PDO;

/**
 * Read-only access to the PassType table.
 *
 * Pass types define multi-event or day passes that can be purchased for a
 * specific event type (e.g. a "Jazz All-Access" day pass).
 */
class PassTypeRepository implements IPassTypeRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Returns all active pass types for the given event type, ordered by ID.
     *
     * @return PassType[]
     */
    public function findByEventType(int $eventTypeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM PassType WHERE EventTypeId = :eventTypeId AND IsActive = 1 ORDER BY PassTypeId ASC'
        );
        $stmt->execute([':eventTypeId' => $eventTypeId]);

        return array_map(
            fn(array $row) => PassType::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }
}

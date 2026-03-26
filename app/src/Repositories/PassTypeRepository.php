<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PassType;
use App\Repositories\Interfaces\IPassTypeRepository;

/**
 * Read-only access to the PassType table.
 *
 * Pass types define multi-event or day passes that can be purchased for a
 * specific event type (e.g. a "Jazz All-Access" day pass).
 */
class PassTypeRepository extends BaseRepository implements IPassTypeRepository
{
    /**
     * Returns all active pass types for the given event type, ordered by ID.
     *
     * @return PassType[]
     */
    public function findByEventType(int $eventTypeId): array
    {
        return $this->fetchAll(
            'SELECT * FROM PassType WHERE EventTypeId = :eventTypeId AND IsActive = 1 ORDER BY PassTypeId ASC',
            ['eventTypeId' => $eventTypeId],
            fn(array $row) => PassType::fromRow($row),
        );
    }
}

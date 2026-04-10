<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PassType;
use App\Repositories\Interfaces\IPassTypeRepository;

class PassTypeRepository extends BaseRepository implements IPassTypeRepository
{
    public function findByEventType(int $eventTypeId): array
    {
        return $this->fetchAll(
            'SELECT * FROM PassType WHERE EventTypeId = :eventTypeId AND IsActive = 1 ORDER BY PassTypeId ASC',
            ['eventTypeId' => $eventTypeId],
            fn(array $row) => PassType::fromRow($row),
        );
    }

    public function findById(int $passTypeId): ?PassType
    {
        return $this->fetchOne(
            'SELECT * FROM PassType WHERE PassTypeId = :passTypeId AND IsActive = 1',
            ['passTypeId' => $passTypeId],
            fn(array $row) => PassType::fromRow($row),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\PassType;

interface IPassTypeRepository
{
    /** @return PassType[] */
    public function findByEventType(int $eventTypeId): array;
}

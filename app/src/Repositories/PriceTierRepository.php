<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PriceTier;
use App\Repositories\Interfaces\IPriceTierRepository;

class PriceTierRepository extends BaseRepository implements IPriceTierRepository
{
    public function findAll(): array
    {
        return $this->fetchAll(
            'SELECT PriceTierId, Name FROM PriceTier ORDER BY PriceTierId ASC',
            [],
            fn(array $row) => PriceTier::fromRow($row),
        );
    }

    public function findById(int $priceTierId): ?PriceTier
    {
        return $this->fetchOne(
            'SELECT PriceTierId, Name FROM PriceTier WHERE PriceTierId = :priceTierId',
            ['priceTierId' => $priceTierId],
            fn(array $row) => PriceTier::fromRow($row),
        );
    }
}

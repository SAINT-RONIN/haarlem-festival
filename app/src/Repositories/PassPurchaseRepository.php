<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IPassPurchaseRepository;

class PassPurchaseRepository extends BaseRepository implements IPassPurchaseRepository
{
    public function create(
        int $passTypeId,
        int $userAccountId,
        ?string $validDate,
        ?string $validFromDate,
        ?string $validToDate,
    ): int {
        return $this->executeInsert(
            'INSERT INTO PassPurchase (PassTypeId, UserAccountId, ValidDate, ValidFromDate, ValidToDate)
            VALUES (:passTypeId, :userAccountId, :validDate, :validFromDate, :validToDate)',
            [
                'passTypeId' => $passTypeId,
                'userAccountId' => $userAccountId,
                'validDate' => $validDate,
                'validFromDate' => $validFromDate,
                'validToDate' => $validToDate,
            ],
        );
    }
}

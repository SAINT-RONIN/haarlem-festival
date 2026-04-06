<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\IPassPurchaseRepository;

/**
 * Manages the PassPurchase table. A PassPurchase is created during checkout
 * when a user purchases a festival pass, and is linked to an OrderItem.
 */
class PassPurchaseRepository extends BaseRepository implements IPassPurchaseRepository
{
    /**
     * Inserts a new pass purchase and returns its auto-generated ID.
     *
     * @throws \RuntimeException If the insert fails.
     */
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

<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Defines persistence operations for pass purchases created during checkout.
 */
interface IPassPurchaseRepository
{
    /**
     * Creates a new pass purchase record and returns its auto-generated ID.
     *
     * @throws \RuntimeException If the insert fails.
     */
    public function create(
        int $passTypeId,
        int $userAccountId,
        ?string $validDate,
        ?string $validFromDate,
        ?string $validToDate,
    ): int;
}

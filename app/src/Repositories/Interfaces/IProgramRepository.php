<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Program;
use App\Models\ProgramFilter;
use App\Models\ProgramItem;
use App\Models\ProgramItemFilter;

interface IProgramRepository
{
    /**
     * @return Program[]
     */
    public function findPrograms(ProgramFilter $filter): array;

    /**
     * @return ProgramItem[]
     */
    public function findProgramItems(ProgramItemFilter $filter): array;

    public function createProgram(string $sessionKey, ?int $userAccountId): Program;

    public function addItem(int $programId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem;

    public function updateItemQuantity(int $programItemId, int $quantity): void;

    public function updateItemDonation(int $programItemId, float $donationAmount): void;

    public function removeItem(int $programItemId): void;

    public function clearProgram(int $programId): void;

    public function markCheckedOut(int $programId): void;
}

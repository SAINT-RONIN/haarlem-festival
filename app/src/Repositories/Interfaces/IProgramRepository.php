<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Program;
use App\Models\ProgramItem;

interface IProgramRepository
{
    /**
     * @param array{
     *   programId?: int,
     *   sessionKey?: string,
     *   userAccountId?: int,
     *   isCheckedOut?: bool
     * } $filters
     * @return Program[]
     */
    public function findPrograms(array $filters = []): array;

    /**
     * @param array{
     *   programId?: int,
     *   programItemId?: int,
     *   eventSessionId?: int
     * } $filters
     * @return ProgramItem[]
     */
    public function findProgramItems(array $filters = []): array;

    public function createProgram(string $sessionKey, ?int $userAccountId): Program;

    public function addItem(int $programId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem;

    public function updateItemQuantity(int $programItemId, int $quantity): void;

    public function updateItemDonation(int $programItemId, float $donationAmount): void;

    public function removeItem(int $programItemId): void;

    public function clearProgram(int $programId): void;
}

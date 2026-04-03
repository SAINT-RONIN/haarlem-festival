<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Program;
use App\DTOs\Filters\ProgramFilter;
use App\Models\ProgramItem;
use App\DTOs\Filters\ProgramItemFilter;

/**
 * Defines persistence operations for programs (shopping carts) and their items.
 */
interface IProgramRepository
{
    /**
     * Queries programs matching the given filter criteria.
     *
     * @return Program[]
     */
    public function findPrograms(ProgramFilter $filter): array;

    /**
     * Queries program items matching the given filter criteria.
     *
     * @return ProgramItem[]
     */
    public function findProgramItems(ProgramItemFilter $filter): array;

    /**
     * Creates a new program for a session key and optional user, returning the Program model.
     *
     * @throws \RuntimeException If the inserted row cannot be read back.
     */
    public function createProgram(string $sessionKey, ?int $userAccountId): Program;

    /**
     * Adds a line item to a program and returns the created ProgramItem.
     *
     * @throws \RuntimeException If the inserted row cannot be read back.
     */
    public function addItem(int $programId, int $eventSessionId, int $quantity, int $priceTierId, float $donationAmount): ProgramItem;

    /**
     * Adds a pass item to a program and returns the created ProgramItem.
     *
     * @throws \RuntimeException If the inserted row cannot be read back.
     */
    public function addPassItem(int $programId, int $passTypeId, ?string $passValidDate, int $quantity, float $donationAmount): ProgramItem;

    /**
     * Updates the ticket quantity on an existing program item.
     */
    public function updateItemQuantity(int $programItemId, int $quantity, int $groupTicketQuantity): void;

    /**
     * Updates the donation amount on an existing program item.
     */
    public function updateItemDonation(int $programItemId, float $donationAmount): void;

    /**
     * Removes a single item from a program.
     */
    public function removeItem(int $programItemId): void;

    /**
     * Removes all items from the given program.
     */
    public function clearProgram(int $programId): void;

    /**
     * Marks a program as checked out so it can no longer be modified.
     */
    public function markCheckedOut(int $programId): void;
}

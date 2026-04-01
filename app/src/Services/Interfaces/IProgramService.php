<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Program;
use App\Content\ProgramMainContent;
use App\DTOs\Program\ProgramData;
use App\Models\ProgramItem;

/**
 * Defines the contract for managing the user's personal festival program (shopping cart).
 */
interface IProgramService
{
    /**
     * Retrieves the existing program for the session/user, or creates a new one if none exists.
     */
    public function getOrCreateProgram(string $sessionKey, ?int $userAccountId): Program;

    /**
     * Adds an event session to the program with the given quantity and optional donation.
     */
    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem;

    /**
     * Adds a festival pass to the program with the given quantity.
     */
    public function addPassToProgram(string $sessionKey, ?int $userAccountId, int $passTypeId, ?string $validDate, int $quantity): ProgramItem;

    /**
     * Updates the ticket quantity for an existing program item.
     */
    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity): void;

    /**
     * Updates the donation amount for an existing program item.
     */
    public function updateDonation(string $sessionKey, ?int $userAccountId, int $programItemId, float $donationAmount): void;

    /**
     * Removes a single item from the program.
     */
    public function removeItem(string $sessionKey, ?int $userAccountId, int $programItemId): void;

    /**
     * Removes all items from the program, effectively emptying the cart.
     */
    public function clearProgram(string $sessionKey, ?int $userAccountId): void;

    /**
     * Returns program with all items enriched with event session details.
     */
    public function getProgramData(string $sessionKey, ?int $userAccountId): ProgramData;

    /**
     * Returns the CMS content for the "My Program" page.
     */
    public function getProgramMainContent(): ProgramMainContent;

    /**
     * Adds a restaurant reservation to the program.
     */
    public function addReservationToProgram(string $sessionKey, ?int $userAccountId, int $reservationId): ProgramItem;
}

<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Program;
use App\Models\ProgramItem;

interface IProgramService
{
    public function getOrCreateProgram(string $sessionKey, ?int $userAccountId): Program;

    public function addToProgram(string $sessionKey, ?int $userAccountId, int $eventSessionId, int $quantity, float $donationAmount): ProgramItem;

    public function updateQuantity(string $sessionKey, ?int $userAccountId, int $programItemId, int $quantity): void;

    public function updateDonation(string $sessionKey, ?int $userAccountId, int $programItemId, float $donationAmount): void;

    public function removeItem(string $sessionKey, ?int $userAccountId, int $programItemId): void;

    public function clearProgram(string $sessionKey, ?int $userAccountId): void;

    /**
     * Returns program with all items enriched with event session details.
     *
     * @return array{program: ?Program, items: array<int, array<string, mixed>>}
     */
    public function getProgramData(string $sessionKey, ?int $userAccountId): array;
}

<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
use App\DTOs\Cms\JazzLineupCardUpsertData;

/**
 * Defines persistence operations for artists.
 */
interface IArtistRepository
{
    /**
     * Returns all artists, optionally filtered by a search term against name fields.
     *
     * @return Artist[]
     */
    public function findAll(?string $search = null): array;

    /**
     * Finds a single artist by its primary key, or null if not found.
     */
    public function findById(int $id): ?Artist;

    /**
     * Inserts a new artist and returns the generated ID.
     */
    public function create(ArtistUpsertData $data): int;

    /**
     * Inserts a Jazz lineup card backed by an Artist row using only card fields.
     */
    public function createJazzOverviewCard(JazzLineupCardUpsertData $data): int;

    /**
     * Updates an existing artist record.
     */
    public function update(int $id, ArtistUpsertData $data): void;

    /**
     * Updates only the Jazz lineup card fields on an Artist row.
     */
    public function updateJazzOverviewCard(int $id, JazzLineupCardUpsertData $data): void;

    /**
     * Returns the next available sort order for a Jazz overview card.
     */
    public function getNextJazzOverviewSortOrder(): int;

    /**
     * Adds or removes an artist from the Jazz overview section.
     */
    public function setJazzOverviewVisibility(int $id, bool $visible): void;

    /**
     * Deletes an artist by its ID.
     */
    public function delete(int $id): void;
}

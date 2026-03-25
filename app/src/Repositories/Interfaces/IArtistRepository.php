<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;

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
     * Updates an existing artist record.
     */
    public function update(int $id, ArtistUpsertData $data): void;

    /**
     * Deletes an artist by its ID.
     */
    public function delete(int $id): void;
}

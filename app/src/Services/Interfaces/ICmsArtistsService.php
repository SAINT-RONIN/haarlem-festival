<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;

/**
 * Defines the contract for CMS artist management (CRUD and validation).
 */
interface ICmsArtistsService
{
    /**
     * Returns all artists, optionally filtered by a search term.
     *
     * @return Artist[]
     */
    public function getArtists(?string $search): array;

    /**
     * Finds a single artist by its ID, or null if not found.
     */
    public function findById(int $id): ?Artist;

    /**
     * Validates fields for creating a new artist, returning a map of field names to error messages.
     *
     * @return array<string, string>
     */
    public function validateForCreate(ArtistUpsertData $data): array;

    /**
     * Validates fields for updating an existing artist, returning a map of field names to error messages.
     *
     * @return array<string, string>
     */
    public function validateForUpdate(int $id, ArtistUpsertData $data): array;

    /**
     * Creates a new artist record and returns the new artist ID.
     */
    public function createArtist(ArtistUpsertData $data): int;

    /**
     * Updates an existing artist record with the given data.
     */
    public function updateArtist(int $id, ArtistUpsertData $data): void;

    /**
     * Deletes an artist by its ID.
     */
    public function deleteArtist(int $id): void;
}

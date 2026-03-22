<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Artist;
use App\Models\ArtistUpsertData;

interface ICmsArtistsService
{
    /** @return Artist[] */
    public function getArtists(?string $search): array;

    public function findById(int $id): ?Artist;

    /** @return array<string, string> */
    public function validateForCreate(ArtistUpsertData $data): array;

    /** @return array<string, string> */
    public function validateForUpdate(int $id, ArtistUpsertData $data): array;

    public function createArtist(ArtistUpsertData $data): int;

    public function updateArtist(int $id, ArtistUpsertData $data): void;

    public function deleteArtist(int $id): void;
}

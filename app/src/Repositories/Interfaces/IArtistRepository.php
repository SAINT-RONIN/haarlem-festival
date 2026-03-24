<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Artist;
use App\Models\ArtistUpsertData;

interface IArtistRepository
{
    /** @return Artist[] */
    public function findAll(?string $search = null): array;

    public function findById(int $id): ?Artist;

    public function create(ArtistUpsertData $data): int;

    public function update(int $id, ArtistUpsertData $data): void;

    public function delete(int $id): void;
}

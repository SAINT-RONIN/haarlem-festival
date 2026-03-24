<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface IDanceRepository
{
    public function getAllArtists(): array;

    public function getArtistBySlug(string $slug): ?array;
}
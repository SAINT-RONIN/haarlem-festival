<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IDanceService
{
    public function getDancePageData(): array;

    public function getArtistDetailBySlug(string $slug): ?array;
}
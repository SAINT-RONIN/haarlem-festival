<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IDanceService
{
    public function getPageData(): array;

    public function getArtistDetailBySlug(string $slug): ?array;
}
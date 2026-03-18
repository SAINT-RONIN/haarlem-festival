<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IJazzArtistDetailService
{
    /**
     * @throws \App\Exceptions\JazzArtistDetailNotFoundException
     */
    public function getArtistPageDataBySlug(string $slug): array;
}

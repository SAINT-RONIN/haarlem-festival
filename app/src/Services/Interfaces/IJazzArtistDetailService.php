<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\JazzArtistDetailPageData;

interface IJazzArtistDetailService
{
    /**
     * @throws \App\Exceptions\JazzArtistDetailNotFoundException
     */
    public function getArtistPageDataBySlug(string $slug): JazzArtistDetailPageData;
}

<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\JazzArtistDetailPageData;

/**
 * Defines the contract for assembling jazz artist detail page data.
 */
interface IJazzArtistDetailService
{
    /**
     * Assembles the full domain payload for a single jazz artist detail page, looked up by URL slug.
     *
     * @throws \App\Exceptions\JazzArtistDetailNotFoundException
     */
    public function getArtistPageDataBySlug(string $slug): JazzArtistDetailPageData;
}

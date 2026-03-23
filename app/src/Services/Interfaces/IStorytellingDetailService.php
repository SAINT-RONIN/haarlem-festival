<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\StorytellingDetailPageData;

/**
 * Defines the contract for assembling storytelling event detail page data.
 */
interface IStorytellingDetailService
{
    /**
     * Assembles the full domain payload for a single storytelling event detail page, looked up by URL slug.
     *
     * @throws \App\Exceptions\StorytellingEventNotFoundException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): StorytellingDetailPageData;
}

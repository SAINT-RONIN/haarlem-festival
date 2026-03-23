<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\StorytellingDetailPageData;

interface IStorytellingDetailService
{
    /**
     * Assembles the full domain payload for a single storytelling event detail page.
     * The reason for this is because the interface enforces the contract so the controller can call this without knowing which concrete implementation fetches and assembles the data.
     *
     * @throws \App\Exceptions\StorytellingEventNotFoundException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): StorytellingDetailPageData;
}

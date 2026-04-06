<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Pages\StorytellingPageData;

/**
 * Defines the contract for assembling the Storytelling overview page data.
 */
interface IStorytellingService
{
    /**
     * Builds the Storytelling overview page domain payload, including CMS content, events, and sessions.
     */
    public function getStorytellingPageData(): StorytellingPageData;
}

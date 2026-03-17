<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\StorytellingPageData;

interface IStorytellingService
{
    /**
     * Builds the Storytelling page domain payload.
     */
    public function getStorytellingPageData(): StorytellingPageData;
}

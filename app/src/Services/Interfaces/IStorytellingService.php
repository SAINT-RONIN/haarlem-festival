<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\StorytellingPageData;

interface IStorytellingService
{
    /**
     * Builds the Storytelling overview page domain payload.
     * The reason for this is because the interface enforces the contract so the controller depends on an abstraction and any implementation can be swapped without touching the controller.
     */
    public function getStorytellingPageData(): StorytellingPageData;
}

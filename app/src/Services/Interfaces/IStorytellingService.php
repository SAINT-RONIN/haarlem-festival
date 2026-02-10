<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Storytelling\StorytellingPageViewModel;

/**
 * Interface for Storytelling page service.
 */
interface IStorytellingService
{
    /**
     * Builds the storytelling page view model with all required data.
     *
     * @return StorytellingPageViewModel Prepared data for the storytelling view
     */
    public function getStorytellingPageData(): StorytellingPageViewModel;
}


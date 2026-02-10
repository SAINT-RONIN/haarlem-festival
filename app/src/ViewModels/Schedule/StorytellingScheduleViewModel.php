<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * ViewModel for the entire storytelling schedule section.
 */
final readonly class StorytellingScheduleViewModel
{
    /**
     * @param string $title Section title from CMS
     * @param string $year Year display from CMS
     * @param string $filtersButtonText Filters button text from CMS
     * @param bool $showFilters Whether to show filters button
     * @param string $additionalInfoTitle Info box title from CMS
     * @param string $additionalInfoBody Info box body HTML from CMS
     * @param bool $showAdditionalInfo Whether to show info box
     * @param string $storyCountLabel Label for story count from CMS
     * @param int $storyCount Total number of events
     * @param bool $showStoryCount Whether to show story count
     * @param string $ctaButtonText Default CTA button text from CMS
     * @param string $payWhatYouLikeText Pay-what-you-like display text from CMS
     * @param string $currencySymbol Currency symbol from CMS
     * @param string $noEventsText Text to show when no events on a day from CMS
     * @param array<StorytellingScheduleDayViewModel> $days Array of day columns
     */
    public function __construct(
        public string $title,
        public string $year,
        public string $filtersButtonText,
        public bool   $showFilters,
        public string $additionalInfoTitle,
        public string $additionalInfoBody,
        public bool   $showAdditionalInfo,
        public string $storyCountLabel,
        public int    $storyCount,
        public bool   $showStoryCount,
        public string $ctaButtonText,
        public string $payWhatYouLikeText,
        public string $currencySymbol,
        public string $noEventsText,
        public array  $days,
    )
    {
    }
}


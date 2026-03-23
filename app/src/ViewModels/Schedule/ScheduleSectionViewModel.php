<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

final readonly class ScheduleSectionViewModel
{
    /**
     * @param array<ScheduleDayViewModel> $days
     * @param ScheduleFilterGroupData[] $filterGroups
     */
    public function __construct(
        public string  $sectionId,
        public string  $title,
        public ?string $year,
        public string  $eventTypeSlug,
        public int     $eventTypeId,
        public string  $filtersButtonText,
        public bool    $showFilters,
        public string  $additionalInfoTitle,
        public string  $additionalInfoBody,
        public bool    $showAdditionalInfo,
        public ?string $eventCountLabel,
        public ?int    $eventCount,
        public bool    $showEventCount,
        public string  $ctaButtonText,
        public string  $payWhatYouLikeText,
        public string  $currencySymbol,
        public string  $noEventsText,
        public array   $days,
        public string  $confirmText = '',
        public string  $addingText = '',
        public string  $successText = '',
        public array   $filterGroups = [],
        public string  $resetButtonText = '',
        public bool    $hasActiveFilters = false,
    ) {
    }
}

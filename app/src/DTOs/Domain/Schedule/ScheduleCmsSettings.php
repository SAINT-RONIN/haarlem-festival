<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** CMS-resolved display settings for the schedule section UI. */
final readonly class ScheduleCmsSettings
{
    public function __construct(
        public string $filtersButtonText,
        public bool   $showFilters,
        public string $additionalInfoTitle,
        public string $additionalInfoBody,
        public bool   $showAdditionalInfo,
        public string $ctaButtonText,
        public string $payWhatYouLikeText,
        public string $currencySymbol,
        public string $noEventsText,
    ) {}
}

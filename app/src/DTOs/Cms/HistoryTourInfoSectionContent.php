<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the History history_important_tour_info_section.
 */
final readonly class HistoryTourInfoSectionContent
{
    public function __construct(
        public ?string $historyImportantTourInfoHeading,
        public ?string $importantInfoItem1,
        public ?string $importantInfoItem2,
        public ?string $importantInfoItem3,
        public ?string $importantInfoItem4,
        public ?string $importantInfoItem5,
        public ?string $importantInfoItem6,
        public ?string $importantInfoItem7,
        public ?string $importantInfoItem8,
    ) {}
}

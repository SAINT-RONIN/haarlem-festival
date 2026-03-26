<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            historyImportantTourInfoHeading: $raw['history_important_tour_info_heading'] ?? null,
            importantInfoItem1: $raw['important_info_item1'] ?? null,
            importantInfoItem2: $raw['important_info_item2'] ?? null,
            importantInfoItem3: $raw['important_info_item3'] ?? null,
            importantInfoItem4: $raw['important_info_item4'] ?? null,
            importantInfoItem5: $raw['important_info_item5'] ?? null,
            importantInfoItem6: $raw['important_info_item6'] ?? null,
            importantInfoItem7: $raw['important_info_item7'] ?? null,
            importantInfoItem8: $raw['important_info_item8'] ?? null,
        );
    }
}

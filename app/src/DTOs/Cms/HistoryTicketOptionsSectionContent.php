<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the History ticket_options_section.
 */
final readonly class HistoryTicketOptionsSectionContent
{
    public function __construct(
        public ?string $ticketOptionsHeading,
        public ?string $historySingleTicketIcon,
        public ?string $historyPricingSingleTitle,
        public ?string $historyPricingSinglePrice,
        public ?string $historyPricingSingleInclude1,
        public ?string $historyPricingSingleInclude2,
        public ?string $historyPricingSingleInclude3,
        public ?string $historyGroupTicketIcon,
        public ?string $historyPricingGroupTitle,
        public ?string $historyPricingGroupPrice,
        public ?string $historyPricingGroupInclude1,
        public ?string $historyPricingGroupInclude2,
        public ?string $historyPricingGroupInclude3,
    ) {}
}

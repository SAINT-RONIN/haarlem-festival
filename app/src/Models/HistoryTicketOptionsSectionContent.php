<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            ticketOptionsHeading: $raw['ticket_options_heading'] ?? null,
            historySingleTicketIcon: $raw['history_single_ticket_icon'] ?? null,
            historyPricingSingleTitle: $raw['history_pricing_single_title'] ?? null,
            historyPricingSinglePrice: $raw['history_pricing_single_price'] ?? null,
            historyPricingSingleInclude1: $raw['history_pricing_single_include1'] ?? null,
            historyPricingSingleInclude2: $raw['history_pricing_single_include2'] ?? null,
            historyPricingSingleInclude3: $raw['history_pricing_single_include3'] ?? null,
            historyGroupTicketIcon: $raw['history_group_ticket_icon'] ?? null,
            historyPricingGroupTitle: $raw['history_pricing_group_title'] ?? null,
            historyPricingGroupPrice: $raw['history_pricing_group_price'] ?? null,
            historyPricingGroupInclude1: $raw['history_pricing_group_include1'] ?? null,
            historyPricingGroupInclude2: $raw['history_pricing_group_include2'] ?? null,
            historyPricingGroupInclude3: $raw['history_pricing_group_include3'] ?? null,
        );
    }
}

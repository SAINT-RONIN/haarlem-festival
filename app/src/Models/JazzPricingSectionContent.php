<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Jazz pricing_section.
 */
final readonly class JazzPricingSectionContent
{
    public function __construct(
        public ?string $pricingHeading,
        public ?string $pricingSubheading,
        public ?string $pricingDescription,
        public ?string $pricingIndividualTitle,
        public ?string $pricingIndividualItem1,
        public ?string $pricingIndividualItem2,
        public ?string $pricingIndividualItem3,
        public ?string $pricingDaypassTitle,
        public ?string $pricingDaypassPrice,
        public ?string $pricingDaypassDesc,
        public ?string $pricingDaypassInclude1,
        public ?string $pricingDaypassInclude2,
        public ?string $pricingDaypassInclude3,
        public ?string $pricingDaypassInclude4,
        public ?string $pricingDaypassInfo,
        public ?string $pricing3dayTitle,
        public ?string $pricing3dayPrice,
        public ?string $pricing3dayDesc,
        public ?string $pricing3dayInclude1,
        public ?string $pricing3dayInclude2,
        public ?string $pricing3dayInclude3,
        public ?string $pricing3dayInclude4,
        public ?string $pricing3dayInfo,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            pricingHeading: $raw['pricing_heading'] ?? null,
            pricingSubheading: $raw['pricing_subheading'] ?? null,
            pricingDescription: $raw['pricing_description'] ?? null,
            pricingIndividualTitle: $raw['pricing_individual_title'] ?? null,
            pricingIndividualItem1: $raw['pricing_individual_item1'] ?? null,
            pricingIndividualItem2: $raw['pricing_individual_item2'] ?? null,
            pricingIndividualItem3: $raw['pricing_individual_item3'] ?? null,
            pricingDaypassTitle: $raw['pricing_daypass_title'] ?? null,
            pricingDaypassPrice: $raw['pricing_daypass_price'] ?? null,
            pricingDaypassDesc: $raw['pricing_daypass_desc'] ?? null,
            pricingDaypassInclude1: $raw['pricing_daypass_include1'] ?? null,
            pricingDaypassInclude2: $raw['pricing_daypass_include2'] ?? null,
            pricingDaypassInclude3: $raw['pricing_daypass_include3'] ?? null,
            pricingDaypassInclude4: $raw['pricing_daypass_include4'] ?? null,
            pricingDaypassInfo: $raw['pricing_daypass_info'] ?? null,
            pricing3dayTitle: $raw['pricing_3day_title'] ?? null,
            pricing3dayPrice: $raw['pricing_3day_price'] ?? null,
            pricing3dayDesc: $raw['pricing_3day_desc'] ?? null,
            pricing3dayInclude1: $raw['pricing_3day_include1'] ?? null,
            pricing3dayInclude2: $raw['pricing_3day_include2'] ?? null,
            pricing3dayInclude3: $raw['pricing_3day_include3'] ?? null,
            pricing3dayInclude4: $raw['pricing_3day_include4'] ?? null,
            pricing3dayInfo: $raw['pricing_3day_info'] ?? null,
        );
    }
}

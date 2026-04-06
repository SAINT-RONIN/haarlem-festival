<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

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
    ) {
    }
}

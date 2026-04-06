<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the History historical_locations_section.
 */
final readonly class HistoryVenuesSectionContent
{
    public function __construct(
        public ?string $historicalLocationsHeading,
        public ?string $historicalLocationsViewMoreLabel,
        public ?string $historyGrotemarktName,
        public ?string $historyGrotemarktDescription,
        public ?string $historyGrotemarktImage,
        public ?string $historyGrotemarktLink,
        public ?string $historyAmsterdamsepoortName,
        public ?string $historyAmsterdamsepoortDescription,
        public ?string $historyAmsterdamsepoortImage,
        public ?string $historyAmsterdamsepoortLink,
        public ?string $historyMolendeadriaanName,
        public ?string $historyMolendeadriaanDescription,
        public ?string $historyMolendeadriaanImage,
        public ?string $historyMolendeadriaanLink,
    ) {}
}

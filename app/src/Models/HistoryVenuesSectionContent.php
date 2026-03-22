<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            historicalLocationsHeading: $raw['historical_locations_heading'] ?? null,
            historicalLocationsViewMoreLabel: $raw['historical_locations_view_more_label'] ?? null,
            historyGrotemarktName: $raw['history_grotemarkt_name'] ?? null,
            historyGrotemarktDescription: $raw['history_grotemarkt_description'] ?? null,
            historyGrotemarktImage: $raw['history_grotemarkt_image'] ?? null,
            historyGrotemarktLink: $raw['history_grotemarkt_link'] ?? null,
            historyAmsterdamsepoortName: $raw['history_amsterdamsepoort_name'] ?? null,
            historyAmsterdamsepoortDescription: $raw['history_amsterdamsepoort_description'] ?? null,
            historyAmsterdamsepoortImage: $raw['history_amsterdamsepoort_image'] ?? null,
            historyAmsterdamsepoortLink: $raw['history_amsterdamsepoort_link'] ?? null,
            historyMolendeadriaanName: $raw['history_molendeadriaan_name'] ?? null,
            historyMolendeadriaanDescription: $raw['history_molendeadriaan_description'] ?? null,
            historyMolendeadriaanImage: $raw['history_molendeadriaan_image'] ?? null,
            historyMolendeadriaanLink: $raw['history_molendeadriaan_link'] ?? null,
        );
    }
}

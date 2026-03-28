<?php

declare(strict_types=1);

namespace App\Content;

/**
 * Carries CMS item values for the Restaurant detail_section.
 */
final readonly class RestaurantDetailSectionContent
{
    public function __construct(
        public ?string $detailContactTitle,
        public ?string $detailLabelAddress,
        public ?string $detailLabelContact,
        public ?string $detailLabelOpenHours,
        public ?string $detailPracticalTitle,
        public ?string $detailLabelPriceFood,
        public ?string $detailLabelRating,
        public ?string $detailLabelSpecialRequests,
        public ?string $detailGalleryTitle,
        public ?string $detailAboutTitlePrefix,
        public ?string $detailChefTitle,
        public ?string $detailMenuTitle,
        public ?string $detailMenuCuisineLabel,
        public ?string $detailLocationTitle,
        public ?string $detailLocationAddressLabel,
        public ?string $detailReservationTitle,
        public ?string $detailReservationDescription,
        public ?string $detailReservationSlotsLabel,
        public ?string $detailReservationNote,
        public ?string $detailReservationBtn,
        public ?string $detailLabelDuration,
        public ?string $detailLabelSeats,
        public ?string $detailLabelFestivalRated,
        public ?string $detailLabelMichelin,
        public ?string $detailMapFallbackText,
        public ?string $detailHeroSubtitleTemplate,
        public ?string $detailHeroBtnPrimary,
        public ?string $detailHeroBtnSecondary,
    ) {}
}

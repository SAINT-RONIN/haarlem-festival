<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            detailContactTitle: $raw['detail_contact_title'] ?? null,
            detailLabelAddress: $raw['detail_label_address'] ?? null,
            detailLabelContact: $raw['detail_label_contact'] ?? null,
            detailLabelOpenHours: $raw['detail_label_open_hours'] ?? null,
            detailPracticalTitle: $raw['detail_practical_title'] ?? null,
            detailLabelPriceFood: $raw['detail_label_price_food'] ?? null,
            detailLabelRating: $raw['detail_label_rating'] ?? null,
            detailLabelSpecialRequests: $raw['detail_label_special_requests'] ?? null,
            detailGalleryTitle: $raw['detail_gallery_title'] ?? null,
            detailAboutTitlePrefix: $raw['detail_about_title_prefix'] ?? null,
            detailChefTitle: $raw['detail_chef_title'] ?? null,
            detailMenuTitle: $raw['detail_menu_title'] ?? null,
            detailMenuCuisineLabel: $raw['detail_menu_cuisine_label'] ?? null,
            detailLocationTitle: $raw['detail_location_title'] ?? null,
            detailLocationAddressLabel: $raw['detail_location_address_label'] ?? null,
            detailReservationTitle: $raw['detail_reservation_title'] ?? null,
            detailReservationDescription: $raw['detail_reservation_description'] ?? null,
            detailReservationSlotsLabel: $raw['detail_reservation_slots_label'] ?? null,
            detailReservationNote: $raw['detail_reservation_note'] ?? null,
            detailReservationBtn: $raw['detail_reservation_btn'] ?? null,
            detailLabelDuration: $raw['detail_label_duration'] ?? null,
            detailLabelSeats: $raw['detail_label_seats'] ?? null,
            detailLabelFestivalRated: $raw['detail_label_festival_rated'] ?? null,
            detailLabelMichelin: $raw['detail_label_michelin'] ?? null,
            detailMapFallbackText: $raw['detail_map_fallback_text'] ?? null,
            detailHeroSubtitleTemplate: $raw['detail_hero_subtitle_template'] ?? null,
            detailHeroBtnPrimary: $raw['detail_hero_btn_primary'] ?? null,
            detailHeroBtnSecondary: $raw['detail_hero_btn_secondary'] ?? null,
        );
    }
}

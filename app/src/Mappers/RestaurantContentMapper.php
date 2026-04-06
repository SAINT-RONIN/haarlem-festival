<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\RestaurantDetailSectionContent;
use App\DTOs\Cms\RestaurantEventCmsData;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;

/**
 * Maps raw CMS arrays into Restaurant page content models.
 */
final class RestaurantContentMapper
{
    /** Maps raw CMS data to a RestaurantCardsSectionContent model. */
    public static function mapCards(array $raw): RestaurantCardsSectionContent
    {
        return new RestaurantCardsSectionContent(
            cardsTitle: $raw['cards_title'] ?? null,
            cardsSubtitle: $raw['cards_subtitle'] ?? null,
        );
    }

    /** Maps raw CMS data to a RestaurantDetailSectionContent model. */
    public static function mapDetail(array $raw): RestaurantDetailSectionContent
    {
        return new RestaurantDetailSectionContent(
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

    /** Maps raw CMS data to a RestaurantIntroSectionContent model. */
    public static function mapIntro(array $raw): RestaurantIntroSectionContent
    {
        return new RestaurantIntroSectionContent(
            introHeading: $raw['intro_heading'] ?? null,
            introBody: $raw['intro_body'] ?? null,
            introImage: $raw['intro_image'] ?? null,
            introImageAlt: $raw['intro_image_alt'] ?? null,
            introClosing: $raw['intro_closing'] ?? null,
        );
    }

    /** Maps raw CMS data to a RestaurantIntroSplit2SectionContent model. */
    public static function mapIntroSplit2(array $raw): RestaurantIntroSplit2SectionContent
    {
        return new RestaurantIntroSplit2SectionContent(
            intro2Heading: $raw['intro2_heading'] ?? null,
            intro2Body: $raw['intro2_body'] ?? null,
            intro2Image: $raw['intro2_image'] ?? null,
            intro2ImageAlt: $raw['intro2_image_alt'] ?? null,
        );
    }

    /** Maps raw CMS data to a RestaurantInstructionsSectionContent model. */
    public static function mapInstructions(array $raw): RestaurantInstructionsSectionContent
    {
        return new RestaurantInstructionsSectionContent(
            instructionsTitle: $raw['instructions_title'] ?? null,
            instructionsCard1Title: $raw['instructions_card_1_title'] ?? null,
            instructionsCard1Text: $raw['instructions_card_1_text'] ?? null,
            instructionsCard2Title: $raw['instructions_card_2_title'] ?? null,
            instructionsCard2Text: $raw['instructions_card_2_text'] ?? null,
            instructionsCard3Title: $raw['instructions_card_3_title'] ?? null,
            instructionsCard3Text: $raw['instructions_card_3_text'] ?? null,
        );
    }

    /** Maps raw CMS data to per-event restaurant CMS content. */
    public static function mapEventCmsData(array $raw): RestaurantEventCmsData
    {
        return RestaurantEventCmsData::fromRawArray($raw);
    }
}

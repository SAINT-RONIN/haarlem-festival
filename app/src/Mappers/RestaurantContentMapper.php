<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\RestaurantCardsSectionContent;
use App\DTOs\Cms\RestaurantInstructionsSectionContent;
use App\DTOs\Cms\RestaurantIntroSectionContent;
use App\DTOs\Cms\RestaurantIntroSplit2SectionContent;
use App\DTOs\Domain\Events\RestaurantRow;
use App\Models\Restaurant;

final class RestaurantContentMapper
{
    public static function mapCards(array $raw): RestaurantCardsSectionContent
    {
        return new RestaurantCardsSectionContent(
            cardsTitle: $raw['cards_title'] ?? null,
            cardsSubtitle: $raw['cards_subtitle'] ?? null,
        );
    }

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

    public static function mapIntroSplit2(array $raw): RestaurantIntroSplit2SectionContent
    {
        return new RestaurantIntroSplit2SectionContent(
            intro2Heading: $raw['intro2_heading'] ?? null,
            intro2Body: $raw['intro2_body'] ?? null,
            intro2Image: $raw['intro2_image'] ?? null,
            intro2ImageAlt: $raw['intro2_image_alt'] ?? null,
        );
    }

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

    /**
     * Maps a RestaurantRow + raw CMS array + resolved image path into a Restaurant model.
     *
     * @param array<string, ?string> $cms Raw CMS key-value pairs for this restaurant
     */
    public static function mapRestaurant(RestaurantRow $row, array $cms, ?string $imagePath): Restaurant
    {
        $cuisineType = $cms['cuisine_type'] ?? null;

        return new Restaurant(
            id: $row->eventId,
            slug: $row->slug,
            name: $row->title,
            shortDescription: $row->shortDescription,
            longDescriptionHtml: $row->longDescriptionHtml,
            featuredImagePath: $imagePath,
            addressLine: $cms['address_line'] ?? null,
            city: $cms['city'] ?? null,
            phone: $cms['phone'] ?? null,
            email: $cms['email'] ?? null,
            website: $cms['website'] ?? null,
            aboutText: $cms['about_text'] ?? null,
            aboutImage: $cms['about_image'] ?? null,
            chefName: $cms['chef_name'] ?? null,
            chefText: $cms['chef_text'] ?? null,
            chefImage: $cms['chef_image'] ?? null,
            cuisineType: $cuisineType,
            cuisineTags: self::parseCuisineTags($cuisineType),
            menuDescription: $cms['menu_description'] ?? null,
            menuImage1: $cms['menu_image_1'] ?? null,
            menuImage2: $cms['menu_image_2'] ?? null,
            locationDescription: $cms['location_description'] ?? null,
            mapEmbedUrl: $cms['map_embed_url'] ?? null,
            stars: max(0, (int) ($cms['stars'] ?? 0)),
            michelinStars: max(0, (int) ($cms['michelin_stars'] ?? 0)),
            seatsPerSession: max(0, (int) ($cms['seats_per_session'] ?? 0)),
            durationMinutes: max(0, (int) ($cms['duration_minutes'] ?? 0)),
            specialRequestsNote: $cms['special_requests_note'] ?? null,
            priceAdult: max(0.0, (float) ($cms['price_adult'] ?? 0)),
            timeSlots: $cms['time_slots'] ?? null,
            reservationImage: $cms['reservation_image'] ?? null,
            galleryImage1: $cms['gallery_image_1'] ?? null,
            galleryImage2: $cms['gallery_image_2'] ?? null,
            galleryImage3: $cms['gallery_image_3'] ?? null,
        );
    }

    /** @return string[] */
    private static function parseCuisineTags(?string $cuisineType): array
    {
        if ($cuisineType === null || trim($cuisineType) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $cuisineType)),
            static fn(string $tag): bool => $tag !== '',
        ));
    }
}

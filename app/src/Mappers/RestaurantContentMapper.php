<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Events\RestaurantRow;
use App\Models\Restaurant;

final class RestaurantContentMapper
{
    /**
     * Maps a RestaurantRow + raw CMS array + resolved image path into a Restaurant model.
     *
     * Structured data (stars, cuisine, price, duration, time slots) comes from the Event row.
     * Content data (about, chef, menu, gallery, etc.) comes from CMS.
     * Address/city comes from Venue (joined in the repository query or passed separately).
     *
     * @param array<string, ?string> $cms Raw CMS key-value pairs for this restaurant
     */
    public static function mapRestaurant(RestaurantRow $row, array $cms, ?string $imagePath, ?string $venueAddress = null, ?string $venueCity = null): Restaurant
    {
        return new Restaurant(
            id: $row->eventId,
            slug: $row->slug,
            name: $row->title,
            shortDescription: $row->shortDescription,
            longDescriptionHtml: $row->longDescriptionHtml,
            featuredImagePath: $imagePath,
            addressLine: $venueAddress,
            city: $venueCity,
            aboutText: self::stripHtml($cms['about_text'] ?? null),
            aboutImage: $cms['about_image'] ?? null,
            chefName: $cms['chef_name'] ?? null,
            chefText: self::stripHtml($cms['chef_text'] ?? null),
            chefImage: $cms['chef_image'] ?? null,
            cuisineType: $row->cuisineType,
            cuisineTags: self::parseCuisineTags($row->cuisineType),
            menuDescription: self::stripHtml($cms['menu_description'] ?? null),
            menuImage1: $cms['menu_image_1'] ?? null,
            menuImage2: $cms['menu_image_2'] ?? null,
            locationDescription: self::stripHtml($cms['location_description'] ?? null),
            mapEmbedUrl: $cms['map_embed_url'] ?? null,
            stars: $row->stars,
            michelinStars: $row->michelinStars,
            seatsPerSession: $row->seatsPerSession,
            durationMinutes: $row->durationMinutes,
            specialRequestsNote: $cms['special_requests_note'] ?? null,
            priceAdult: $row->priceAdult,
            reservationFee: $row->reservationFee,
            timeSlots: $row->timeSlots,
            reservationImage: $cms['reservation_image'] ?? null,
            galleryImage1: $cms['gallery_image_1'] ?? null,
            galleryImage2: $cms['gallery_image_2'] ?? null,
            galleryImage3: $cms['gallery_image_3'] ?? null,
        );
    }

    /** Strips HTML tags from CMS content, returns null if empty. */
    private static function stripHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $clean = trim(strip_tags($value));
        return $clean !== '' ? $clean : null;
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

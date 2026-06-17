<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\RestaurantPageConstants;

/**
 * Core domain model for a restaurant.
 *
 * Built from three sources via fromDbRow():
 *   - Event table columns (structured data)
 *   - CMS key-value pairs (editorial content)
 *   - Resolved MediaAsset file path (featured image)
 */
final readonly class Restaurant
{
    /**
     * @param string[] $cuisineTags Parsed cuisine tags (e.g. ['Italian', 'Vegan'])
     * @param string[] $timeSlots   Parsed time slot strings (e.g. ['17:00', '19:00'])
     * @param string[] $menuImages  Non-empty menu image paths
     * @param string[] $galleryImages Non-empty gallery image paths
     */
    public function __construct(
        // ── Core (from Event table) ──
        public int     $id,
        public string  $slug,
        public string  $name,
        public string  $shortDescription,
        public string  $longDescriptionHtml,
        public ?string $featuredImagePath,

        // ── Contact (from Venue join) ──
        public ?string $addressLine,
        public ?string $city,
        public string  $fullAddress,

        // ── About (from CMS) ──
        public ?string $aboutText,
        public ?string $aboutImage,

        // ── Chef (from CMS) ──
        public ?string $chefName,
        public ?string $chefText,
        public ?string $chefImage,

        // ── Menu (from Event + CMS) ──
        public ?string $cuisineType,
        public array   $cuisineTags,
        public ?string $menuDescription,
        public array   $menuImages,

        // ── Location (from CMS) ──
        public ?string $locationDescription,
        public ?string $mapEmbedUrl,

        // ── Practical info (from Event) ──
        public int     $stars,
        public int     $michelinStars,
        public int     $seatsPerSession,
        public int     $durationMinutes,
        public ?string $specialRequestsNote,

        // ── Reservation (from Event + CMS) ──
        public float   $priceAdult,
        public float   $reservationFee,
        public array   $timeSlots,

        // ── Gallery (from CMS) ──
        public array   $galleryImages,
    ) {}

    /**
     * Builds a Restaurant from a raw DB row, CMS key-value pairs, and a resolved image path.
     *
     * @param array<string, mixed>   $row       Raw Event + Venue JOIN row
     * @param array<string, ?string> $cms       CMS key-value pairs for this restaurant
     * @param ?string                $imagePath Resolved featured image file path
     */
    public static function fromDbRow(array $row, array $cms, ?string $imagePath): self
    {
        $addressLine = $row['VenueAddressLine'] ?? null;
        $city = $row['VenueCity'] ?? null;

        return new self(
            id: (int) ($row['EventId'] ?? 0),
            slug: (string) ($row['Slug'] ?? ''),
            name: (string) ($row['Title'] ?? ''),
            shortDescription: (string) ($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string) ($row['LongDescriptionHtml'] ?? ''),
            featuredImagePath: $imagePath,
            addressLine: $addressLine,
            city: $city,
            fullAddress: self::buildFullAddress($addressLine, $city),
            aboutText: self::stripHtml($cms['about_text'] ?? null),
            aboutImage: $cms['about_image'] ?? null,
            chefName: $cms['chef_name'] ?? null,
            chefText: self::stripHtml($cms['chef_text'] ?? null),
            chefImage: $cms['chef_image'] ?? null,
            cuisineType: $row['CuisineType'] ?? null,
            cuisineTags: self::parseCuisineTags($row['CuisineType'] ?? null),
            menuDescription: self::stripHtml($cms['menu_description'] ?? null),
            menuImages: self::collectNonEmpty([$cms['menu_image_1'] ?? null, $cms['menu_image_2'] ?? null]),
            locationDescription: self::stripHtml($cms['location_description'] ?? null),
            mapEmbedUrl: $cms['map_embed_url'] ?? null,
            stars: max(0, (int) ($row['Stars'] ?? 0)),
            michelinStars: max(0, (int) ($row['MichelinStars'] ?? 0)),
            seatsPerSession: max(0, (int) ($row['SeatsPerSession'] ?? 0)),
            durationMinutes: max(0, (int) ($row['DurationMinutes'] ?? 0)),
            specialRequestsNote: $cms['special_requests_note'] ?? null,
            priceAdult: max(0.0, (float) ($row['PriceAdult'] ?? 0)),
            reservationFee: RestaurantPageConstants::RESERVATION_FEE_PER_GUEST,
            timeSlots: self::parseTimeSlots($row['TimeSlots'] ?? null),
            galleryImages: self::collectNonEmpty([
                $cms['gallery_image_1'] ?? null,
                $cms['gallery_image_2'] ?? null,
                $cms['gallery_image_3'] ?? null,
            ]),
        );
    }

    // ── Private helpers ──────────────────────────────────────────────

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

    /** @return string[] */
    private static function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $raw)),
            static fn(string $slot): bool => $slot !== '',
        ));
    }

    /**
     * @param (?string)[] $values
     * @return string[]
     */
    private static function collectNonEmpty(array $values): array
    {
        $result = [];
        foreach ($values as $value) {
            if ($value !== null && trim($value) !== '') {
                $result[] = $value;
            }
        }
        return $result;
    }

    private static function buildFullAddress(?string $addressLine, ?string $city): string
    {
        $parts = array_filter(
            [trim((string) $addressLine), trim((string) $city)],
            static fn(string $part): bool => $part !== '',
        );

        return implode(', ', $parts);
    }
}

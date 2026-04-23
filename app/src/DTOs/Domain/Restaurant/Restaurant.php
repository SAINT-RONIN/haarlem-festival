<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Restaurant;

/**
 * Core domain model for a restaurant.
 *
 * Replaces the old RestaurantDetailEvent + RestaurantEventCmsData split.
 * All restaurant data lives in one place: event fields from the DB row
 * and CMS content from the per-event CMS section.
 */
final readonly class Restaurant
{
    public function __construct(
        // ── Core (from events table) ──
        public int    $id,
        public string $slug,
        public string $name,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public ?string $featuredImagePath,

        // ── Contact ──
        public ?string $addressLine,
        public ?string $city,
        public ?string $phone,
        public ?string $email,
        public ?string $website,

        // ── About ──
        public ?string $aboutText,
        public ?string $aboutImage,

        // ── Chef ──
        public ?string $chefName,
        public ?string $chefText,
        public ?string $chefImage,

        // ── Menu ──
        public ?string $cuisineType,
        public ?string $menuDescription,
        public ?string $menuImage1,
        public ?string $menuImage2,

        // ── Location ──
        public ?string $locationDescription,
        public ?string $mapEmbedUrl,

        // ── Practical info ──
        public ?string $stars,
        public ?string $michelinStars,
        public ?string $seatsPerSession,
        public ?string $durationMinutes,
        public ?string $specialRequestsNote,

        // ── Reservation ──
        public ?string $priceAdult,
        public ?string $timeSlots,
        public ?string $reservationImage,

        // ── Gallery ──
        public ?string $galleryImage1,
        public ?string $galleryImage2,
        public ?string $galleryImage3,
    ) {}

    /**
     * Build a Restaurant from a DB event row, a CMS key-value array,
     * and the resolved featured-image path.
     *
     * @param array<string, mixed>   $row  Event row from the database
     * @param array<string, ?string> $cms  CMS item values keyed by item key
     * @param ?string                $imagePath Resolved featured-image path
     */
    public static function fromRowAndCms(array $row, array $cms, ?string $imagePath = null): self
    {
        return new self(
            id:                  (int) ($row['EventId'] ?? throw new \InvalidArgumentException('Missing EventId')),
            slug:                (string) ($row['Slug'] ?? throw new \InvalidArgumentException('Missing Slug')),
            name:                (string) ($row['Title'] ?? throw new \InvalidArgumentException('Missing Title')),
            shortDescription:    (string) ($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string) ($row['LongDescriptionHtml'] ?? ''),
            featuredImagePath:   $imagePath,

            addressLine:         $cms['address_line'] ?? null,
            city:                $cms['city'] ?? null,
            phone:               $cms['phone'] ?? null,
            email:               $cms['email'] ?? null,
            website:             $cms['website'] ?? null,

            aboutText:           $cms['about_text'] ?? null,
            aboutImage:          $cms['about_image'] ?? null,

            chefName:            $cms['chef_name'] ?? null,
            chefText:            $cms['chef_text'] ?? null,
            chefImage:           $cms['chef_image'] ?? null,

            cuisineType:         $cms['cuisine_type'] ?? null,
            menuDescription:     $cms['menu_description'] ?? null,
            menuImage1:          $cms['menu_image_1'] ?? null,
            menuImage2:          $cms['menu_image_2'] ?? null,

            locationDescription: $cms['location_description'] ?? null,
            mapEmbedUrl:         $cms['map_embed_url'] ?? null,

            stars:               $cms['stars'] ?? null,
            michelinStars:       $cms['michelin_stars'] ?? null,
            seatsPerSession:     $cms['seats_per_session'] ?? null,
            durationMinutes:     $cms['duration_minutes'] ?? null,
            specialRequestsNote: $cms['special_requests_note'] ?? null,

            priceAdult:          $cms['price_adult'] ?? null,
            timeSlots:           $cms['time_slots'] ?? null,
            reservationImage:    $cms['reservation_image'] ?? null,

            galleryImage1:       $cms['gallery_image_1'] ?? null,
            galleryImage2:       $cms['gallery_image_2'] ?? null,
            galleryImage3:       $cms['gallery_image_3'] ?? null,
        );
    }
}

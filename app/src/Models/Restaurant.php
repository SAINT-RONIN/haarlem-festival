<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Core domain model for a restaurant.
 *
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

        // ── About ──
        public ?string $aboutText,
        public ?string $aboutImage,

        // ── Chef ──
        public ?string $chefName,
        public ?string $chefText,
        public ?string $chefImage,

        // ── Menu ──
        public ?string $cuisineType,
        /** @var string[] Parsed cuisine tags (e.g. ['Italian', 'Vegan']) */
        public array $cuisineTags,
        public ?string $menuDescription,
        public ?string $menuImage1,
        public ?string $menuImage2,

        // ── Location ──
        public ?string $locationDescription,
        public ?string $mapEmbedUrl,

        // ── Practical info (typed at the model level) ──
        public int $stars,
        public int $michelinStars,
        public int $seatsPerSession,
        public int $durationMinutes,
        public ?string $specialRequestsNote,

        // ── Reservation ──
        public float $priceAdult,
        public ?string $timeSlots,
        public ?string $reservationImage,

        // ── Gallery ──
        public ?string $galleryImage1,
        public ?string $galleryImage2,
        public ?string $galleryImage3,
    ) {}
}

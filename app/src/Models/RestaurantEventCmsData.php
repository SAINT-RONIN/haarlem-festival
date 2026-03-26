<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries all per-restaurant CMS content for a single restaurant event detail page.
 *
 * Mirrors StorytellingEventCmsData: content is loaded from a per-event CMS section
 * keyed by RestaurantDetailConstants::eventSectionKey($eventId).
 *
 * Shared UI labels (section titles, button text, etc.) live in RestaurantDetailSectionContent
 * so they are edited once and apply to all restaurants.
 *
 * Pricing: only price_adult is stored; price for under-12 is always half the adult price.
 */
final readonly class RestaurantEventCmsData
{
    public function __construct(
        // Contact
        public ?string $addressLine,
        public ?string $city,
        public ?string $phone,
        public ?string $email,
        public ?string $website,

        // About section
        public ?string $aboutText,
        public ?string $aboutImage,

        // Chef section
        public ?string $chefName,
        public ?string $chefText,
        public ?string $chefImage,

        // Menu section
        public ?string $cuisineType,
        public ?string $menuDescription,
        public ?string $menuImage1,
        public ?string $menuImage2,

        // Location section
        public ?string $locationDescription,
        public ?string $mapEmbedUrl,

        // Practical info
        public ?string $stars,
        public ?string $michelinStars,
        public ?string $seatsPerSession,
        public ?string $durationMinutes,
        public ?string $specialRequestsNote,

        // Reservation
        public ?string $priceAdult,
        public ?string $timeSlots,
        public ?string $reservationImage,

        // Gallery
        public ?string $galleryImage1,
        public ?string $galleryImage2,
        public ?string $galleryImage3,
    ) {
    }

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            addressLine:         $raw['address_line']          ?? null,
            city:                $raw['city']                  ?? null,
            phone:               $raw['phone']                 ?? null,
            email:               $raw['email']                 ?? null,
            website:             $raw['website']               ?? null,
            aboutText:           $raw['about_text']            ?? null,
            aboutImage:          $raw['about_image']           ?? null,
            chefName:            $raw['chef_name']             ?? null,
            chefText:            $raw['chef_text']             ?? null,
            chefImage:           $raw['chef_image']            ?? null,
            cuisineType:         $raw['cuisine_type']          ?? null,
            menuDescription:     $raw['menu_description']      ?? null,
            menuImage1:          $raw['menu_image_1']          ?? null,
            menuImage2:          $raw['menu_image_2']          ?? null,
            locationDescription: $raw['location_description']  ?? null,
            mapEmbedUrl:         $raw['map_embed_url']         ?? null,
            stars:               $raw['stars']                 ?? null,
            michelinStars:       $raw['michelin_stars']        ?? null,
            seatsPerSession:     $raw['seats_per_session']     ?? null,
            durationMinutes:     $raw['duration_minutes']      ?? null,
            specialRequestsNote: $raw['special_requests_note'] ?? null,
            priceAdult:          $raw['price_adult']           ?? null,
            timeSlots:           $raw['time_slots']            ?? null,
            reservationImage:    $raw['reservation_image']     ?? null,
            galleryImage1:       $raw['gallery_image_1']       ?? null,
            galleryImage2:       $raw['gallery_image_2']       ?? null,
            galleryImage3:       $raw['gallery_image_3']       ?? null,
        );
    }
}
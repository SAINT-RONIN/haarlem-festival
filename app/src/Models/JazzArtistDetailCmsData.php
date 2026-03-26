<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for a single Jazz artist detail page.
 * Same pattern as StorytellingEventCmsData.
 */
final readonly class JazzArtistDetailCmsData
{
    public function __construct(
        public ?string $heroSubtitle,
        public ?string $heroBackgroundImage,
        public ?string $originText,
        public ?string $formedText,
        public ?string $performancesText,
        public ?string $heroBackButtonText,
        public ?string $heroBackButtonUrl,
        public ?string $heroReserveButtonText,
        public ?string $overviewHeading,
        public ?string $overviewLead,
        public ?string $overviewBodyPrimary,
        public ?string $overviewBodySecondary,
        public ?string $lineupHeading,
        public ?string $highlightsHeading,
        public ?string $photoGalleryHeading,
        public ?string $photoGalleryDescription,
        public ?string $albumsHeading,
        public ?string $albumsDescription,
        public ?string $listenHeading,
        public ?string $listenSubheading,
        public ?string $listenDescription,
        public ?string $listenPlayButtonLabel,
        public ?string $listenPlayExcerptText,
        public ?string $listenTrackArtworkAltSuffix,
        public ?string $liveCtaHeading,
        public ?string $liveCtaDescription,
        public ?string $liveCtaBookButtonText,
        public ?string $liveCtaScheduleButtonText,
        public ?string $liveCtaScheduleButtonUrl,
        public ?string $performancesSectionId,
        public ?string $performancesHeading,
        public ?string $performancesDescription,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
            originText: $raw['origin_text'] ?? null,
            formedText: $raw['formed_text'] ?? null,
            performancesText: $raw['performances_text'] ?? null,
            heroBackButtonText: $raw['hero_back_button_text'] ?? null,
            heroBackButtonUrl: $raw['hero_back_button_url'] ?? null,
            heroReserveButtonText: $raw['hero_reserve_button_text'] ?? null,
            overviewHeading: $raw['overview_heading'] ?? null,
            overviewLead: $raw['overview_lead'] ?? null,
            overviewBodyPrimary: $raw['overview_body_primary'] ?? null,
            overviewBodySecondary: $raw['overview_body_secondary'] ?? null,
            lineupHeading: $raw['lineup_heading'] ?? null,
            highlightsHeading: $raw['highlights_heading'] ?? null,
            photoGalleryHeading: $raw['photo_gallery_heading'] ?? null,
            photoGalleryDescription: $raw['photo_gallery_description'] ?? null,
            albumsHeading: $raw['albums_heading'] ?? null,
            albumsDescription: $raw['albums_description'] ?? null,
            listenHeading: $raw['listen_heading'] ?? null,
            listenSubheading: $raw['listen_subheading'] ?? null,
            listenDescription: $raw['listen_description'] ?? null,
            listenPlayButtonLabel: $raw['listen_play_button_label'] ?? null,
            listenPlayExcerptText: $raw['listen_play_excerpt_text'] ?? null,
            listenTrackArtworkAltSuffix: $raw['listen_track_artwork_alt_suffix'] ?? null,
            liveCtaHeading: $raw['live_cta_heading'] ?? null,
            liveCtaDescription: $raw['live_cta_description'] ?? null,
            liveCtaBookButtonText: $raw['live_cta_book_button_text'] ?? null,
            liveCtaScheduleButtonText: $raw['live_cta_schedule_button_text'] ?? null,
            liveCtaScheduleButtonUrl: $raw['live_cta_schedule_button_url'] ?? null,
            performancesSectionId: $raw['performances_section_id'] ?? null,
            performancesHeading: $raw['performances_heading'] ?? null,
            performancesDescription: $raw['performances_description'] ?? null,
        );
    }
}

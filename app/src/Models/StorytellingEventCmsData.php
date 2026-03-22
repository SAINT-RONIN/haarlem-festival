<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the CMS item values for a single storytelling event detail page.
 * Extracted from the raw key/value array returned by the CMS repository so
 * the rest of the application can use typed property access.
 */
final readonly class StorytellingEventCmsData
{
    public function __construct(
        public ?string $heroImage,
        public ?string $backButtonLabel,
        public ?string $reserveButtonLabel,
        public ?string $aboutHeading,
        public ?string $aboutBody,
        public ?string $aboutImage1,
        public ?string $aboutImage2,
        public ?string $highlightsHeading,
        public ?string $highlight1Title,
        public ?string $highlight1Image,
        public ?string $highlight1Description,
        public ?string $highlight2Title,
        public ?string $highlight2Image,
        public ?string $highlight2Description,
        public ?string $highlight3Title,
        public ?string $highlight3Image,
        public ?string $highlight3Description,
        public ?string $galleryHeading,
        public ?string $gallery1Image,
        public ?string $gallery2Image,
        public ?string $gallery3Image,
        public ?string $gallery4Image,
        public ?string $gallery5Image,
        public ?string $videoHeading,
        public ?string $videoUrl,
        public ?string $videoPlaceholder,
        public ?string $scheduleCtaButtonText,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            heroImage: $raw['hero_image'] ?? null,
            backButtonLabel: $raw['back_button_label'] ?? null,
            reserveButtonLabel: $raw['reserve_button_label'] ?? null,
            aboutHeading: $raw['about_heading'] ?? null,
            aboutBody: $raw['about_body'] ?? null,
            aboutImage1: $raw['about_image_1'] ?? null,
            aboutImage2: $raw['about_image_2'] ?? null,
            highlightsHeading: $raw['highlights_heading'] ?? null,
            highlight1Title: $raw['highlight_1_title'] ?? null,
            highlight1Image: $raw['highlight_1_image'] ?? null,
            highlight1Description: $raw['highlight_1_description'] ?? null,
            highlight2Title: $raw['highlight_2_title'] ?? null,
            highlight2Image: $raw['highlight_2_image'] ?? null,
            highlight2Description: $raw['highlight_2_description'] ?? null,
            highlight3Title: $raw['highlight_3_title'] ?? null,
            highlight3Image: $raw['highlight_3_image'] ?? null,
            highlight3Description: $raw['highlight_3_description'] ?? null,
            galleryHeading: $raw['gallery_heading'] ?? null,
            gallery1Image: $raw['gallery_image_1'] ?? null,
            gallery2Image: $raw['gallery_image_2'] ?? null,
            gallery3Image: $raw['gallery_image_3'] ?? null,
            gallery4Image: $raw['gallery_image_4'] ?? null,
            gallery5Image: $raw['gallery_image_5'] ?? null,
            videoHeading: $raw['video_heading'] ?? null,
            videoUrl: $raw['video_url'] ?? null,
            videoPlaceholder: $raw['video_placeholder'] ?? null,
            scheduleCtaButtonText: $raw['schedule_cta_button_text'] ?? null,
        );
    }
}

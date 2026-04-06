<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

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
}

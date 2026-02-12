<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for the intro text + image split section.
 *
 * All fields are guaranteed to be populated by the Service layer.
 *
 * Some pages (e.g. Restaurant) optionally provide structured subsections
 * and a closing line.
 */
final readonly class IntroSplitSectionData
{
    /**
     * @param array<int, array{heading: string, text: string}>|null $subsections
     */
    public function __construct(
        public string $headingText,
        public string $bodyText,
        public string $imageUrl,
        public string $imageAltText = 'Stories in Haarlem',
        public ?array $subsections = null,
        public ?string $closingLine = null,
    )
    {
    }
}

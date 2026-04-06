<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Band lineup section for a jazz artist detail page — section title and member list.
 */
final readonly class JazzArtistLineupData
{
    /**
     * @param string[] $lineup
     * @param string[] $highlights
     * @param string[] $galleryImages
     */
    public function __construct(
        public string $lineupHeading,
        public array $lineup,
        public string $highlightsHeading,
        public array $highlights,
        public string $photoGalleryHeading,
        public string $photoGalleryDescription,
        public array $galleryImages,
    ) {}
}

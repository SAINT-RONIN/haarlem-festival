<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

/**
 * DTO for the festival experience slider/gallery.
 */
final readonly class ExperienceData
{
    /**
     * @param string[] $imageUrls
     */
    public function __construct(
        public string $title,
        public array $imageUrls,
    ) {
    }
}


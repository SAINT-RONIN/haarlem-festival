<?php

declare(strict_types=1);

namespace App\ViewModels\Dance;

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
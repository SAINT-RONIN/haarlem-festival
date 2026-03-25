<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Restaurant intro_split2_section.
 */
final readonly class RestaurantIntroSplit2SectionContent
{
    public function __construct(
        public ?string $intro2Heading,
        public ?string $intro2Body,
        public ?string $intro2Image,
        public ?string $intro2ImageAlt,
    ) {}
}

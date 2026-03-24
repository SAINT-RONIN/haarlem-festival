<?php

declare(strict_types=1);

namespace App\Models;

final readonly class DanceGradientSectionContent
{
    public function __construct(
        public ?string $gradientHeading,
        public ?string $gradientSubheading,
        public ?string $gradientBackgroundImage,
    ) {
    }

    public static function fromRawArray(array $raw): self
    {
        return new self(
            gradientHeading: $raw['gradient_heading'] ?? null,
            gradientSubheading: $raw['gradient_subheading'] ?? null,
            gradientBackgroundImage: $raw['gradient_background_image'] ?? null,
        );
    }
}
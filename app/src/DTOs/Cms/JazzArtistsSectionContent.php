<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the Jazz artists_section.
 */
final readonly class JazzArtistsSectionContent
{
    public function __construct(
        public ?string $artistsHeading,
    ) {
    }
}

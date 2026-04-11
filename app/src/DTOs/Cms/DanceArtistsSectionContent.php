<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the Dance artists_section (supporting artists).
 */
final readonly class DanceArtistsSectionContent
{
    public function __construct(
        public ?string $artistsHeading,
    ) {}
}

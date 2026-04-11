<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\DanceArtistsSectionContent;
use App\DTOs\Cms\DanceHeadlinersSectionContent;

/**
 * Maps raw CMS arrays into Dance page content models.
 */
final class DanceContentMapper
{
    /** Maps raw CMS data to a DanceHeadlinersSectionContent model. */
    public static function mapHeadliners(array $raw): DanceHeadlinersSectionContent
    {
        return new DanceHeadlinersSectionContent(
            headlinersHeading: $raw['headliners_heading'] ?? null,
        );
    }

    /** Maps raw CMS data to a DanceArtistsSectionContent model. */
    public static function mapArtists(array $raw): DanceArtistsSectionContent
    {
        return new DanceArtistsSectionContent(
            artistsHeading: $raw['artists_heading'] ?? null,
        );
    }
}

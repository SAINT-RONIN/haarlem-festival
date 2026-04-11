<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Cms\DanceArtistsSectionContent;
use App\DTOs\Cms\DanceHeadlinersSectionContent;

/**
 * Typed access to Dance page CMS content sections.
 */
interface IDanceContentRepository
{
    /** Fetches the dance headliners section content. */
    public function findHeadlinersContent(string $pageSlug, string $sectionKey): DanceHeadlinersSectionContent;

    /** Fetches the dance supporting artists section content. */
    public function findArtistsContent(string $pageSlug, string $sectionKey): DanceArtistsSectionContent;
}

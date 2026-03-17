<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Domain payload for the Storytelling overview page.
 */
final readonly class StorytellingPageData
{
    /**
     * @param array<string, array<string, mixed>> $sections CMS section content keyed by section name
     * @param array<string, mixed> $scheduleSectionData Raw schedule data
     */
    public function __construct(
        public array $sections,
        public array $scheduleSectionData,
    ) {
    }
}

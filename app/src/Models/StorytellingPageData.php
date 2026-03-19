<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries all CMS sections needed to render the Storytelling overview page.
 * The reason for this is because the service must return a typed object rather than a raw array so the mapper and controller always know exactly what fields are available.
 */
final readonly class StorytellingPageData
{
    /**
     * @param array<string, array<string, mixed>> $sections CMS section content keyed by section name
     * @param array<string, mixed> $globalUiContent CMS content for the global_ui section
     */
    public function __construct(
        public array $sections,
        public array $globalUiContent,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * CMS content for a page's intro section (headline, description, background image).
 * Shared across Jazz and Storytelling pages that use the same intro layout.
 * Hydrated from CMS key-value pairs via the domain content repository.
 */
final readonly class IntroSectionContent
{
    public function __construct(
        public ?string $introHeading,
        public ?string $introBody,
        public ?string $introImage,
        public ?string $introImageAlt,
    ) {
    }
}

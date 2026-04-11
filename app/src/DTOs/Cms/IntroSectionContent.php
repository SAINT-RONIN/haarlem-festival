<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * CMS content for a page's intro section (headline, body, image, optional label).
 * Shared across Jazz, Dance, History, and Storytelling pages.
 * Hydrated from CMS key-value pairs via the domain content repository.
 */
final readonly class IntroSectionContent
{
    public function __construct(
        public ?string $introHeading,
        public ?string $introBody,
        public ?string $introImage,
        public ?string $introImageAlt,
        public ?string $introLabel = null,
    ) {}
}

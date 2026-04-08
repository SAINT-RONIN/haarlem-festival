<?php

declare(strict_types=1);

namespace App\View;

/**
 * Layout settings for public pages rendered inside the shared shell.
 */
final readonly class PublicPageLayout
{
    /**
     * @param list<ViewTemplate> $contentTemplates
     */
    public function __construct(
        public array $contentTemplates = [],
        public bool $includeHero = true,
        public bool $includeEventSections = false,
        public ?string $eventIntroSectionId = null,
        public ?string $eventIntroImageClass = null,
        public string $mainClass = 'w-full bg-sand inline-flex flex-col justify-start items-center',
    ) {}
}

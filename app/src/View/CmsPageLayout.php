<?php

declare(strict_types=1);

namespace App\View;

/**
 * Layout settings for CMS pages rendered inside the shared CMS shell.
 *
 * Mirrors PublicPageLayout for the admin side. Every CMS page constructs one
 * of these and passes it to partials/cms/_shell.php via ViewRenderer::render.
 */
final readonly class CmsPageLayout
{
    /**
     * @param list<string> $extraScripts  Page-specific <script src=""> tags injected before </body>.
     */
    public function __construct(
        public string $title,
        public string $currentView,
        public ViewTemplate $content,
        public bool $includeFlashMessages = true,
        public bool $includeLucide = true,
        public string $mainClass = 'flex-1 p-8 overflow-auto',
        public array $extraScripts = [],
    ) {}
}

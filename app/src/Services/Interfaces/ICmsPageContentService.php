<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICmsPageContentService
{
    /** @return array<string, ?string> */
    public function getSectionContent(string $pageSlug, string $sectionKey): array;
}

<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICmsPageContentService
{
    public function getSectionContent(string $pageSlug, string $sectionKey): array;
}

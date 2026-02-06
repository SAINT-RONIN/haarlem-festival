<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

interface ICmsRepository
{
    public function getPageBySlug(string $slug): ?array;
    public function getSectionsByPageId(int $cmsPageId): array;
    public function getItemsBySectionId(int $cmsSectionId): array;
    public function getItemsBySectionKey(int $cmsPageId, string $sectionKey): array;
}


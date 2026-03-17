<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICmsService
{
    public function getHomePageContent(): array;

    public function getSectionContent(string $pageSlug, string $sectionKey): array;

    public function getHeroSectionContent(string $pageSlug): array;
}

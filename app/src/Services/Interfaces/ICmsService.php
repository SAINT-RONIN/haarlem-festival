<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

interface ICmsService
{
    public function getHomePageContent(): array;

    public function getSectionContent(string $pageSlug, string $sectionKey): array;

    public function getHeroSectionContent(string $pageSlug): array;

    public function buildHeroData(string $pageSlug, string $currentPage): HeroData;

    /**
     * @return array{content: array, isLoggedIn: bool}
     */
    public function getGlobalUiContent(): array;

    public function buildGlobalUiData(): GlobalUiData;
}

<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

class CmsMapper
{
    public static function toHeroData(array $heroContent, string $currentPage): HeroData
    {
        return HeroData::fromCms($heroContent, $currentPage);
    }

    public static function toGlobalUiData(array $globalUiContent, bool $isLoggedIn): GlobalUiData
    {
        return GlobalUiData::fromCms($globalUiContent, $isLoggedIn);
    }
}

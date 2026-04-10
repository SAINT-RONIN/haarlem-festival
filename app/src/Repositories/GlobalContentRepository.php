<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\GlobalContentMapper;
use App\DTOs\Cms\GlobalUiContent;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Cms\HeroSectionContent;
use App\DTOs\Cms\IntroSectionContent;

class GlobalContentRepository extends BaseContentRepository implements Interfaces\IGlobalContentRepository
{
    public function findGlobalUiContent(string $pageSlug, string $sectionKey): GlobalUiContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapGlobalUi($raw);
    }

    public function findHeroContent(string $pageSlug): HeroSectionContent
    {
        $raw = $this->cmsContent->getHeroSectionContent($pageSlug);
        return GlobalContentMapper::mapHero($raw);
    }

    public function findHeroContentBySection(string $pageSlug, string $sectionKey): HeroSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapHero($raw);
    }

    // Re-maps already-fetched raw hero data (avoids double-fetching after existence check).
    public function mapHeroFromRaw(array $raw): HeroSectionContent
    {
        return GlobalContentMapper::mapHero($raw);
    }

    public function findGradientContent(string $pageSlug, string $sectionKey): GradientSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapGradient($raw);
    }

    public function findIntroContent(string $pageSlug, string $sectionKey): IntroSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return GlobalContentMapper::mapIntro($raw);
    }
}

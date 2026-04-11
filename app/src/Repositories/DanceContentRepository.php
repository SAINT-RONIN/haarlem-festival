<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Cms\DanceArtistsSectionContent;
use App\DTOs\Cms\DanceHeadlinersSectionContent;
use App\Mappers\DanceContentMapper;
use App\Repositories\Interfaces\IDanceContentRepository;

class DanceContentRepository extends BaseContentRepository implements IDanceContentRepository
{
    public function findHeadlinersContent(string $pageSlug, string $sectionKey): DanceHeadlinersSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return DanceContentMapper::mapHeadliners($raw);
    }

    public function findArtistsContent(string $pageSlug, string $sectionKey): DanceArtistsSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return DanceContentMapper::mapArtists($raw);
    }
}

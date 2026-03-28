<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\CmsSectionEditData;
use App\Models\CmsPage;

interface ICmsPreviewUrlResolver
{
    /**
     * @param CmsSectionEditData[] $sections
     */
    public function resolve(CmsPage $page, array $sections): string;
}

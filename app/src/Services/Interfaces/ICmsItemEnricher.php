<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Cms\CmsItemEditData;
use App\Models\CmsItem;

interface ICmsItemEnricher
{
    /**
     * @param CmsItem[] $items
     * @return CmsItemEditData[]
     */
    public function enrichItems(array $items): array;
}

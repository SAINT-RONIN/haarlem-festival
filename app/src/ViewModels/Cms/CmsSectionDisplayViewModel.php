<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single CMS section in the page editor — section title, editable items, and sub-groups.
 */
final readonly class CmsSectionDisplayViewModel
{
    /**
     * @param CmsItemDisplayViewModel[]   $items
     * @param CmsSubGroupViewModel[]|null $subGroups
     */
    public function __construct(
        public int    $id,
        public string $key,
        public string $displayName,
        public bool   $isEditable,
        public array  $items,
        public ?array $subGroups,
    ) {}
}

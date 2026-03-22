<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsPageEditViewModel
{
    /**
     * @param array{id: int, title: string, slug: string} $page
     * @param array<int, array<string, mixed>> $sections
     * @param array<string, int> $contentLimits
     * @param array<string, mixed> $imageLimits
     */
    public function __construct(
        public array $page,
        public array $sections,
        public array $contentLimits,
        public array $imageLimits,
    ) {}
}

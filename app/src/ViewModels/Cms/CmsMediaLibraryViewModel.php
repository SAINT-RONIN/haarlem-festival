<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsMediaLibraryViewModel
{
    /**
     * @param CmsMediaListItemViewModel[] $assets
     * @param array<string, mixed> $imageLimits
     */
    public function __construct(
        public array $assets,
        public array $imageLimits,
        public string $csrfToken,
        public ?string $successMessage,
        public ?string $errorMessage,
    ) {}
}

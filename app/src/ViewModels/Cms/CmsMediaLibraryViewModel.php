<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS media library page (media.php).
 *
 * Carries asset list, upload limits, and CSRF token.
 */
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

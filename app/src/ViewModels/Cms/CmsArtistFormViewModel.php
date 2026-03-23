<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS artist create/edit form.
 *
 * Carries form action URL, field values, validation errors, and CSRF token.
 */
final readonly class CmsArtistFormViewModel
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        public ?int    $artistId,
        public string  $name,
        public string  $style,
        public string  $bioHtml,
        public ?int    $imageAssetId,
        public bool    $isActive,
        public string  $csrfToken,
        public string  $formAction,
        public string  $pageTitle,
        public array   $errors,
    ) {}
}

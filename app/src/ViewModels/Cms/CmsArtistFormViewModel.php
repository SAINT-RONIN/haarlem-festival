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
        public string  $cardDescription,
        public string  $heroSubtitle,
        public string  $heroImagePath,
        public string  $originText,
        public string  $formedText,
        public string  $bioHtml,
        public string  $overviewLead,
        public string  $overviewBodySecondary,
        public string  $lineupHeading,
        public string  $highlightsHeading,
        public string  $photoGalleryHeading,
        public string  $photoGalleryDescription,
        public string  $albumsHeading,
        public string  $albumsDescription,
        public string  $listenHeading,
        public string  $listenSubheading,
        public string  $listenDescription,
        public string  $liveCtaHeading,
        public string  $liveCtaDescription,
        public string  $performancesHeading,
        public string  $performancesDescription,
        public int     $cardSortOrder,
        public bool    $showOnJazzOverview,
        public ?int    $imageAssetId,
        public bool    $isActive,
        public string  $csrfToken,
        public string  $formAction,
        public string  $returnTo,
        public string  $backUrl,
        public string  $pageTitle,
        public array   $errors,
    ) {}
}

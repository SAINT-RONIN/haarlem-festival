<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
use App\ViewModels\Cms\CmsArtistFormViewModel;
use App\ViewModels\Cms\CmsArtistListItemViewModel;
use App\ViewModels\Cms\CmsArtistsListViewModel;

/**
 * Transforms Artist domain models into ViewModels for the CMS artist-management pages
 * (artist list and artist create/edit form).
 */
final class CmsArtistsMapper
{
    public static function emptyData(): ArtistUpsertData
    {
        return new ArtistUpsertData(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Band Lineup',
            'Career Highlights',
            'Photo Gallery',
            '',
            'Featured Albums',
            '',
            'LISTEN NOW',
            'Important Tracks',
            '',
            '',
            '',
            '',
            '',
            0,
            false,
            null,
            true
        );
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromFormInput(array $input): ArtistUpsertData
    {
        return new ArtistUpsertData(
            name: (string) ($input['name'] ?? ''),
            style: (string) ($input['style'] ?? ''),
            cardDescription: (string) ($input['cardDescription'] ?? ''),
            heroSubtitle: (string) ($input['heroSubtitle'] ?? ''),
            heroImagePath: (string) ($input['heroImagePath'] ?? ''),
            originText: (string) ($input['originText'] ?? ''),
            formedText: (string) ($input['formedText'] ?? ''),
            bioHtml: (string) ($input['bioHtml'] ?? ''),
            overviewLead: (string) ($input['overviewLead'] ?? ''),
            overviewBodySecondary: (string) ($input['overviewBodySecondary'] ?? ''),
            lineupHeading: (string) ($input['lineupHeading'] ?? ''),
            highlightsHeading: (string) ($input['highlightsHeading'] ?? ''),
            photoGalleryHeading: (string) ($input['photoGalleryHeading'] ?? ''),
            photoGalleryDescription: (string) ($input['photoGalleryDescription'] ?? ''),
            albumsHeading: (string) ($input['albumsHeading'] ?? ''),
            albumsDescription: (string) ($input['albumsDescription'] ?? ''),
            listenHeading: (string) ($input['listenHeading'] ?? ''),
            listenSubheading: (string) ($input['listenSubheading'] ?? ''),
            listenDescription: (string) ($input['listenDescription'] ?? ''),
            liveCtaHeading: (string) ($input['liveCtaHeading'] ?? ''),
            liveCtaDescription: (string) ($input['liveCtaDescription'] ?? ''),
            performancesHeading: (string) ($input['performancesHeading'] ?? ''),
            performancesDescription: (string) ($input['performancesDescription'] ?? ''),
            cardSortOrder: (int) ($input['cardSortOrder'] ?? 0),
            showOnJazzOverview: (bool) ($input['showOnJazzOverview'] ?? false),
            imageAssetId: isset($input['imageAssetId']) ? (int) $input['imageAssetId'] : null,
            isActive: (bool) ($input['isActive'] ?? false),
        );
    }

    /**
     * Converts an array of Artist models into the full CMS artist-list page ViewModel,
     * including search state, flash messages, and a CSRF token for delete actions.
     *
     * @param Artist[] $artists
     */
    public static function toListViewModel(
        array $artists,
        string $searchQuery,
        ?string $successMessage,
        ?string $errorMessage,
        string $deleteCsrfToken,
    ): CmsArtistsListViewModel {
        return new CmsArtistsListViewModel(
            items: array_map([self::class, 'toListItemViewModel'], $artists),
            searchQuery: $searchQuery,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
            deleteCsrfToken: $deleteCsrfToken,
        );
    }

    /**
     * Builds the CMS artist create/edit form ViewModel from upsert data and validation errors.
     * Used for both the "new artist" and "edit artist" pages (distinguished by $artistId).
     *
     * @param array<string, string> $errors
     */
    public static function toFormViewModel(
        ?int $artistId,
        ArtistUpsertData $data,
        string $csrfToken,
        string $formAction,
        string $returnTo,
        string $backUrl,
        string $pageTitle,
        array $errors,
    ): CmsArtistFormViewModel {
        return new CmsArtistFormViewModel(
            artistId: $artistId,
            name: $data->name,
            style: $data->style,
            cardDescription: $data->cardDescription,
            heroSubtitle: $data->heroSubtitle,
            heroImagePath: $data->heroImagePath,
            originText: $data->originText,
            formedText: $data->formedText,
            bioHtml: $data->bioHtml,
            overviewLead: $data->overviewLead,
            overviewBodySecondary: $data->overviewBodySecondary,
            lineupHeading: $data->lineupHeading,
            highlightsHeading: $data->highlightsHeading,
            photoGalleryHeading: $data->photoGalleryHeading,
            photoGalleryDescription: $data->photoGalleryDescription,
            albumsHeading: $data->albumsHeading,
            albumsDescription: $data->albumsDescription,
            listenHeading: $data->listenHeading,
            listenSubheading: $data->listenSubheading,
            listenDescription: $data->listenDescription,
            liveCtaHeading: $data->liveCtaHeading,
            liveCtaDescription: $data->liveCtaDescription,
            performancesHeading: $data->performancesHeading,
            performancesDescription: $data->performancesDescription,
            cardSortOrder: $data->cardSortOrder,
            showOnJazzOverview: $data->showOnJazzOverview,
            imageAssetId: $data->imageAssetId,
            isActive: $data->isActive,
            csrfToken: $csrfToken,
            formAction: $formAction,
            returnTo: $returnTo,
            backUrl: $backUrl,
            pageTitle: $pageTitle,
            errors: $errors,
        );
    }

    /**
     * Extracts the editable fields from a persisted Artist into an ArtistUpsertData DTO,
     * used to pre-populate the edit form with current values.
     */
    public static function fromArtist(Artist $a): ArtistUpsertData
    {
        return new ArtistUpsertData(
            name: $a->name,
            style: $a->style,
            cardDescription: $a->cardDescription,
            heroSubtitle: $a->heroSubtitle,
            heroImagePath: $a->heroImagePath,
            originText: $a->originText,
            formedText: $a->formedText,
            bioHtml: $a->bioHtml,
            overviewLead: $a->overviewLead,
            overviewBodySecondary: $a->overviewBodySecondary,
            lineupHeading: $a->lineupHeading,
            highlightsHeading: $a->highlightsHeading,
            photoGalleryHeading: $a->photoGalleryHeading,
            photoGalleryDescription: $a->photoGalleryDescription,
            albumsHeading: $a->albumsHeading,
            albumsDescription: $a->albumsDescription,
            listenHeading: $a->listenHeading,
            listenSubheading: $a->listenSubheading,
            listenDescription: $a->listenDescription,
            liveCtaHeading: $a->liveCtaHeading,
            liveCtaDescription: $a->liveCtaDescription,
            performancesHeading: $a->performancesHeading,
            performancesDescription: $a->performancesDescription,
            cardSortOrder: $a->cardSortOrder,
            showOnJazzOverview: $a->showOnJazzOverview,
            imageAssetId: $a->imageAssetId,
            isActive: $a->isActive,
        );
    }

    private static function toListItemViewModel(Artist $a): CmsArtistListItemViewModel
    {
        return new CmsArtistListItemViewModel(
            artistId: $a->artistId,
            name: $a->name,
            style: $a->style,
            isActive: $a->isActive,
            createdAt: $a->createdAtUtc->format(FormatHelper::SHORT_DATE),
        );
    }
}

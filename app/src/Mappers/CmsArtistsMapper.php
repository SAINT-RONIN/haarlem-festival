<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\Models\Artist;
use App\Models\ArtistUpsertData;
use App\ViewModels\Cms\CmsArtistFormViewModel;
use App\ViewModels\Cms\CmsArtistListItemViewModel;
use App\ViewModels\Cms\CmsArtistsListViewModel;

final class CmsArtistsMapper
{
    /**
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
     * @param array<string, string> $errors
     */
    public static function toFormViewModel(
        ?int $artistId,
        ArtistUpsertData $data,
        string $csrfToken,
        string $formAction,
        string $pageTitle,
        array $errors,
    ): CmsArtistFormViewModel {
        return new CmsArtistFormViewModel(
            artistId: $artistId,
            name: $data->name,
            style: $data->style,
            bioHtml: $data->bioHtml,
            imageAssetId: $data->imageAssetId,
            isActive: $data->isActive,
            csrfToken: $csrfToken,
            formAction: $formAction,
            pageTitle: $pageTitle,
            errors: $errors,
        );
    }

    public static function fromArtist(Artist $a): ArtistUpsertData
    {
        return new ArtistUpsertData(
            name: $a->name,
            style: $a->style,
            bioHtml: $a->bioHtml,
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

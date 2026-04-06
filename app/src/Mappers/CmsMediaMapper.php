<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\Models\MediaAsset;
use App\ViewModels\Cms\CmsMediaListItemViewModel;

/**
 * Transforms MediaAsset domain models into ViewModels and JSON payloads
 * consumed by the CMS media library and media-picker endpoints.
 */
final class CmsMediaMapper
{
    /**
     * Converts a MediaAsset into a plain array suitable for JSON API responses
     * (e.g. the media-picker AJAX endpoint in the CMS).
     */
    public static function toMediaJsonData(MediaAsset $asset): array
    {
        return [
            'mediaAssetId'     => $asset->mediaAssetId,
            'filePath'         => $asset->filePath,
            'originalFileName' => $asset->originalFileName,
            'mimeType'         => $asset->mimeType,
        ];
    }

    /**
     * Converts a MediaAsset into a display-ready list-item ViewModel for the CMS media grid,
     * formatting the file size and creation date for human readability.
     */
    public static function toMediaListItemViewModel(MediaAsset $asset): CmsMediaListItemViewModel
    {
        return new CmsMediaListItemViewModel(
            mediaAssetId: $asset->mediaAssetId,
            filePath: $asset->filePath,
            originalFileName: $asset->originalFileName,
            mimeType: $asset->mimeType,
            fileSize: FormatHelper::fileSize($asset->fileSizeBytes),
            altText: $asset->altText,
            createdAt: $asset->createdAtUtc->format(FormatHelper::CMS_DATE_FORMAT),
        );
    }
}

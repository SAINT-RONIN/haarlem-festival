<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Exceptions\ValidationException;

/**
 * Interface for Media asset service.
 */
interface IMediaAssetService
{
    /**
     * Uploads an image file and creates a database record.
     *
     * @param array $file The $_FILES array element
     * @param string $folder Subfolder within Image directory
     * @return array The created MediaAsset record
     * @throws ValidationException If validation fails
     */
    public function uploadImage(array $file, string $folder = 'cms'): array;

    /**
     * Links an uploaded media asset to a CMS item.
     *
     * @param int $mediaAssetId Media asset ID
     * @param int $cmsItemId CMS item ID
     * @return bool Success status
     */
    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool;

    /**
     * Gets the image validation limits for client-side validation.
     *
     * @return array Validation limits
     */
    public function getImageLimits(): array;
}


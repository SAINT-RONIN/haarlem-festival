<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Infrastructure\PathResolver;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IMediaAssetService;
use App\Utils\CmsContentLimits;

/**
 * Manages the full lifecycle of uploaded media assets (images).
 *
 * Handles validation (MIME type, file size, dimensions via CmsContentLimits),
 * physical file storage under /assets/Image/{folder}/, database record creation
 * via IMediaAssetRepository, and cleanup (file + record) on deletion.
 * Also exposes validation limits so the admin frontend can mirror them client-side.
 */
class MediaAssetService implements IMediaAssetService
{
    public function __construct(
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {
    }

    /**
     * Uploads an image file and creates a database record.
     *
     * @param array $file The $_FILES array element
     * @param string $folder Subfolder within Image directory
     * @return MediaAsset The created MediaAsset record
     * @throws ValidationException If validation fails
     */
    public function uploadImage(array $file, string $folder = 'cms'): MediaAsset
    {
        $this->validateFile($file);

        $targetDir = $this->ensureUploadDirectory($folder);
        $relativePath = $this->moveUploadedFile($file, $targetDir, $folder);

        return $this->persistMediaRecord($file, $relativePath);
    }

    /** Ensures the target upload directory exists and is writable. */
    private function ensureUploadDirectory(string $folder): string
    {
        $targetDir = PathResolver::getUploadPath($folder);

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
            throw new ValidationException('Failed to create upload directory');
        }

        if (!is_writable($targetDir)) {
            throw new ValidationException('Upload directory is not writable');
        }

        return $targetDir;
    }

    /** Generates a unique filename, moves the uploaded file, and returns the relative path. */
    private function moveUploadedFile(array $file, string $targetDir, string $folder): string
    {
        $extension = $this->getExtensionFromMime($file['type']);
        $fileName = $this->generateFileName($extension);
        $filePath = $targetDir . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new ValidationException('Failed to move uploaded file');
        }

        return '/assets/Image/' . $folder . '/' . $fileName;
    }

    /** Persists the asset metadata in the database and returns the full record. */
    private function persistMediaRecord(array $file, string $relativePath): MediaAsset
    {
        $mediaAssetId = $this->mediaAssetRepository->create([
            'FilePath' => $relativePath,
            'OriginalFileName' => $file['name'],
            'MimeType' => $file['type'],
            'FileSizeBytes' => $file['size'],
            'AltText' => '',
        ]);

        return $this->mediaAssetRepository->findById($mediaAssetId);
    }

    /**
     * Deletes a media asset record and removes the physical file from disk.
     * Returns false if the asset does not exist.
     */
    public function deleteAsset(int $mediaAssetId): bool
    {
        $asset = $this->mediaAssetRepository->findById($mediaAssetId);
        if (!$asset) {
            return false;
        }

        $filePath = PathResolver::getPublicPath() . $asset->filePath;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->mediaAssetRepository->delete($mediaAssetId);
    }

    /**
     * Gets a media asset by ID.
     */
    public function getAssetById(int $mediaAssetId): ?MediaAsset
    {
        return $this->mediaAssetRepository->findById($mediaAssetId);
    }

    /**
     * Updates the alt text for a media asset.
     */
    public function updateAltText(int $mediaAssetId, string $altText): bool
    {
        return $this->mediaAssetRepository->update($mediaAssetId, [
            'AltText' => $altText
        ]);
    }

    /**
     * Validates an uploaded file.
     *
     * @throws ValidationException If validation fails
     */
    /**
     * Validates an uploaded file for errors, MIME type, file size, and image dimensions.
     *
     * @throws ValidationException If validation fails
     */
    private function validateFile(array $file): void
    {
        $this->checkUploadError($file);
        $this->checkMimeType($file['type']);
        $this->checkFileSize($file['size']);
        $this->checkImageDimensions($file['tmp_name']);
    }

    /** Rejects files with upload errors. */
    private function checkUploadError(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException($this->getUploadErrorMessage($file['error']));
        }
    }

    /** Rejects files with disallowed MIME types. */
    private function checkMimeType(string $mimeType): void
    {
        if (!in_array($mimeType, CmsContentLimits::IMAGE_ALLOWED_MIMES, true)) {
            throw new ValidationException('Invalid file type. Allowed: JPG, PNG, WebP');
        }
    }

    /** Rejects files exceeding the maximum file size. */
    private function checkFileSize(int $size): void
    {
        if ($size > CmsContentLimits::IMAGE_MAX_FILE_SIZE) {
            throw new ValidationException(
                'File too large. Maximum size: ' . $this->formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE)
            );
        }
    }

    /** Rejects images exceeding the maximum width or height. Skips if dimensions cannot be read. */
    private function checkImageDimensions(string $tmpName): void
    {
        $imageInfo = @getimagesize($tmpName);
        if ($imageInfo === false) {
            return;
        }

        [$width, $height] = $imageInfo;

        if ($width > CmsContentLimits::IMAGE_MAX_WIDTH) {
            throw new ValidationException(
                "Image width ({$width}px) exceeds maximum of " . CmsContentLimits::IMAGE_MAX_WIDTH . 'px'
            );
        }

        if ($height > CmsContentLimits::IMAGE_MAX_HEIGHT) {
            throw new ValidationException(
                "Image height ({$height}px) exceeds maximum of " . CmsContentLimits::IMAGE_MAX_HEIGHT . 'px'
            );
        }
    }

    /**
     * Generates a unique filename.
     */
    private function generateFileName(string $extension): string
    {
        return uniqid('img_', true) . '.' . $extension;
    }

    /**
     * Gets file extension from MIME type.
     */
    private function getExtensionFromMime(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };
    }

    /**
     * Formats file size in human-readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }

    /**
     * Gets a human-readable upload error message.
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
            default => 'Unknown upload error'
        };
    }

    /**
     * Links an uploaded media asset to a CMS item.
     *
     * @param int $mediaAssetId Media asset ID
     * @param int $cmsItemId CMS item ID
     * @return bool Success status
     */
    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool
    {
        return $this->mediaAssetRepository->linkToCmsItem($mediaAssetId, $cmsItemId);
    }

    /**
     * Returns all media assets.
     *
     * @return MediaAsset[]
     */
    public function getAllAssets(): array
    {
        return $this->mediaAssetRepository->findAll();
    }

    /**
     * Gets the image validation limits for client-side validation.
     *
     * @return array Validation limits
     */
    public function getImageLimits(): array
    {
        return [
            'maxFileSize' => CmsContentLimits::IMAGE_MAX_FILE_SIZE,
            'maxFileSizeFormatted' => $this->formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE),
            'maxWidth' => CmsContentLimits::IMAGE_MAX_WIDTH,
            'maxHeight' => CmsContentLimits::IMAGE_MAX_HEIGHT,
            'allowedMimes' => CmsContentLimits::IMAGE_ALLOWED_MIMES,
            'allowedExtensions' => ['jpg', 'jpeg', 'png', 'webp'],
        ];
    }
}

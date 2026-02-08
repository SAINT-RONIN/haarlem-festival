<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\MediaAssetRepository;
use App\Utils\CmsContentLimits;
use App\Exceptions\ValidationException;

/**
 * Service for handling media asset operations.
 *
 * Manages image uploads, validation, and storage.
 */
class MediaAssetService
{
    private MediaAssetRepository $mediaAssetRepository;
    private string $uploadBasePath;

    public function __construct()
    {
        $this->mediaAssetRepository = new MediaAssetRepository();
        
        // Determine the public directory path
        // In Docker: /app/public, locally: use __DIR__ relative path
        if (is_dir('/app/public')) {
            $publicPath = '/app/public';
        } else {
            $publicPath = realpath(__DIR__ . '/../../public') ?: __DIR__ . '/../../public';
        }
        
        $this->uploadBasePath = $publicPath . '/assets/Image';
    }

    /**
     * Uploads an image file and creates a database record.
     *
     * @param array $file The $_FILES array element
     * @param string $folder Subfolder within Image directory
     * @return array The created MediaAsset record
     * @throws ValidationException If validation fails
     */
    public function uploadImage(array $file, string $folder = 'cms'): array
    {
        $this->validateFile($file);

        $targetDir = $this->uploadBasePath . '/' . $folder;
        
        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new ValidationException('Failed to create upload directory');
            }
        }
        
        // Check if directory is writable
        if (!is_writable($targetDir)) {
            throw new ValidationException('Upload directory is not writable');
        }

        $extension = $this->getExtensionFromMime($file['type']);
        $fileName = $this->generateFileName($extension);
        $filePath = $targetDir . '/' . $fileName;
        $relativePath = '/assets/Image/' . $folder . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new ValidationException('Failed to move uploaded file');
        }

        $mediaAssetId = $this->mediaAssetRepository->create([
            'FilePath' => $relativePath,
            'OriginalFileName' => $file['name'],
            'MimeType' => $file['type'],
            'FileSizeBytes' => $file['size'],
            'AltText' => ''
        ]);

        return $this->mediaAssetRepository->findById($mediaAssetId);
    }

    /**
     * Deletes a media asset and its file.
     */
    public function deleteAsset(int $mediaAssetId): bool
    {
        $asset = $this->mediaAssetRepository->findById($mediaAssetId);
        if (!$asset) {
            return false;
        }

        // Determine the public directory path
        if (is_dir('/app/public')) {
            $publicPath = '/app/public';
        } else {
            $publicPath = realpath(__DIR__ . '/../../public') ?: __DIR__ . '/../../public';
        }
        
        $filePath = $publicPath . $asset['FilePath'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->mediaAssetRepository->delete($mediaAssetId);
    }

    /**
     * Gets a media asset by ID.
     */
    public function getAssetById(int $mediaAssetId): ?array
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
    private function validateFile(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException($this->getUploadErrorMessage($file['error']));
        }

        if (!in_array($file['type'], CmsContentLimits::IMAGE_ALLOWED_MIMES, true)) {
            throw new ValidationException(
                'Invalid file type. Allowed: JPG, PNG, WebP'
            );
        }

        if ($file['size'] > CmsContentLimits::IMAGE_MAX_FILE_SIZE) {
            throw new ValidationException(
                'File too large. Maximum size: ' . 
                $this->formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE)
            );
        }

        // Try to get image dimensions - if this fails, skip dimension validation
        // getimagesize works without GD for most image formats
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo !== false) {
            [$width, $height] = $imageInfo;
            if ($width > CmsContentLimits::IMAGE_MAX_WIDTH) {
                throw new ValidationException(
                    "Image width ({$width}px) exceeds maximum of " . 
                    CmsContentLimits::IMAGE_MAX_WIDTH . 'px'
                );
            }

            if ($height > CmsContentLimits::IMAGE_MAX_HEIGHT) {
                throw new ValidationException(
                    "Image height ({$height}px) exceeds maximum of " . 
                    CmsContentLimits::IMAGE_MAX_HEIGHT . 'px'
                );
            }
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
}


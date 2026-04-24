<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Infrastructure\PathResolver;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Services\Interfaces\IMediaAssetService;
use App\Utils\CmsContentLimits;

class MediaAssetService implements IMediaAssetService
{
    public function __construct(
        private readonly IMediaAssetRepository $mediaAssetRepository,
    ) {}

    /** @param array $file The $_FILES array element @throws ValidationException */
    public function uploadImage(array $file, string $folder = 'cms'): MediaAsset
    {
        $this->validateFile($file);

        $targetDir = $this->ensureUploadDirectory($folder);
        $relativePath = $this->moveUploadedFile($file, $targetDir, $folder);

        return $this->persistMediaRecord($file, $relativePath);
    }

    private function ensureUploadDirectory(string $folder): string
    {
        $targetDir = PathResolver::getUploadPath($folder);

        if (!is_dir($targetDir) && !mkdir($targetDir, 0o755, true)) {
            throw new ValidationException('Failed to create upload directory');
        }

        if (!is_writable($targetDir)) {
            throw new ValidationException('Upload directory is not writable');
        }

        return $targetDir;
    }

    // Returns path starting with "/assets/" for direct use in <img src="...">.
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

    // AltText stored as empty string on creation; can be updated later.
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

    // Authorization must be enforced at the controller level before calling this.
    public function deleteAsset(int $mediaAssetId): bool
    {
        if ($mediaAssetId <= 0) {
            return false;
        }

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

    public function getAssetById(int $mediaAssetId): ?MediaAsset
    {
        return $this->mediaAssetRepository->findById($mediaAssetId);
    }

    public function updateAltText(int $mediaAssetId, string $altText): bool
    {
        return $this->mediaAssetRepository->update($mediaAssetId, [
            'AltText' => $altText,
        ]);
    }

    /** @throws ValidationException */
    private function validateFile(array $file): void
    {
        $this->checkUploadError($file);
        $this->checkMimeType($file['type']);
        $this->checkFileSize($file['size']);
        $this->checkImageDimensions($file['tmp_name']);
    }

    private function checkUploadError(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException($this->getUploadErrorMessage($file['error']));
        }
    }

    private function checkMimeType(string $mimeType): void
    {
        if (!in_array($mimeType, CmsContentLimits::IMAGE_ALLOWED_MIMES, true)) {
            throw new ValidationException('Invalid file type. Allowed: JPG, PNG, WebP');
        }
    }

    private function checkFileSize(int $size): void
    {
        if ($size > CmsContentLimits::IMAGE_MAX_FILE_SIZE) {
            throw new ValidationException(
                'File too large. Maximum size: ' . $this->formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE)
            );
        }
    }

    // Silently skipped when dimensions can't be read (MIME/size already validated).
    private function checkImageDimensions(string $tmpName): void
    {
        // @ suppresses PHP warnings from getimagesize when the temp file is unreadable — we already handle false.
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

    private function generateFileName(string $extension): string
    {
        return uniqid('img_', true) . '.' . $extension;
    }

    private function getExtensionFromMime(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {  // 1048576 = 1 MB (1024 * 1024)
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';  // 1024 = 1 KB
    }

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

    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool
    {
        return $this->mediaAssetRepository->linkToCmsItem($mediaAssetId, $cmsItemId);
    }

    /** @return MediaAsset[] */
    public function getAllAssets(): array
    {
        return $this->mediaAssetRepository->findAll();
    }

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

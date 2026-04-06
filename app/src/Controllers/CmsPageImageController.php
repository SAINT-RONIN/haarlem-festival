<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\CmsMessages;
use App\Exceptions\CmsEditException;
use App\Exceptions\ValidationException;
use App\Models\MediaAsset;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;

class CmsPageImageController extends CmsBaseController
{
    private const CSRF_SCOPE = 'cms_page_edit';
    private const MEDIA_CONTEXT = 'cms';

    public function __construct(
        ISessionService $sessionService,
        private readonly ICmsEditService $cmsEditService,
        private readonly IMediaAssetService $mediaAssetService,
    ) {
        parent::__construct($sessionService);
    }

    public function uploadImage(int $id): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $this->processUpload();
        });
    }

    private function processUpload(): void
    {
        $this->validateUploadCsrf();
        $itemId = $this->readOptionalIntPostParam('item_id') ?? 0;
        if ($itemId > 0) { $this->handleItemUpload($itemId); return; }
        $this->uploadTinyMceImage();
    }

    private function validateUploadCsrf(): void
    {
        if ($this->sessionService->isValidCsrfToken(self::CSRF_SCOPE, $this->readStringPostParam('_csrf'))) {
            return;
        }
        throw new ValidationException(CmsMessages::INVALID_CSRF);
    }

    private function handleItemUpload(int $itemId): void
    {
        $mediaAssetId = $this->readOptionalIntPostParam('media_asset_id') ?? 0;
        if ($mediaAssetId > 0) {
            $this->linkExistingAsset($itemId, $mediaAssetId);
            return;
        }
        $this->uploadAndLinkImage($itemId);
    }

    private function linkExistingAsset(int $itemId, int $mediaAssetId): void
    {
        $this->updateItemImageOrFail($itemId, $mediaAssetId);
        $this->json($this->linkedAssetResponse($mediaAssetId));
    }

    private function uploadAndLinkImage(int $itemId): void
    {
        $mediaAsset = $this->uploadImageFile();
        $this->updateItemImageOrFail($itemId, $mediaAsset->mediaAssetId);
        $this->json($this->uploadedAssetResponse($mediaAsset));
    }

    private function updateItemImageOrFail(int $itemId, int $mediaAssetId): void
    {
        try {
            $this->cmsEditService->updateItemImage($itemId, $mediaAssetId);
        } catch (CmsEditException $error) {
            throw new ValidationException(CmsMessages::IMAGE_LINK_FAILED, 0, $error);
        }
    }

    private function uploadTinyMceImage(): void
    {
        $this->json(['success' => true, 'filePath' => $this->uploadImageFile()->filePath]);
    }

    private function uploadImageFile(): MediaAsset
    {
        return $this->mediaAssetService->uploadImage($this->requireImageFile(), self::MEDIA_CONTEXT);
    }

    private function requireImageFile(): array
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            return $_FILES['image'];
        }
        throw new ValidationException(CmsMessages::NO_FILE_UPLOADED);
    }

    private function linkedAssetResponse(int $mediaAssetId): array
    {
        return ['success' => true, 'mediaAssetId' => $mediaAssetId, 'filePath' => $this->mediaAssetService->getAssetById($mediaAssetId)?->filePath ?? '', 'message' => CmsMessages::IMAGE_LINK_SUCCESS];
    }

    private function uploadedAssetResponse(MediaAsset $mediaAsset): array
    {
        return ['success' => true, 'mediaAssetId' => $mediaAsset->mediaAssetId, 'filePath' => $mediaAsset->filePath, 'message' => CmsMessages::IMAGE_UPLOAD_SUCCESS];
    }
}

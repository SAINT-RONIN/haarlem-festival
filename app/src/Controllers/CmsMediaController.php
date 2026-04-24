<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Mappers\CmsMediaMapper;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsMediaLibraryViewModel;

/**
 * CMS controller for the media asset library (upload, browse, delete).
 * Provides both page views and JSON endpoints for AJAX pickers.
 */
class CmsMediaController extends CmsBaseController
{
    public function __construct(
        private readonly IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'media';
            $viewModel = $this->buildMediaListViewModel();
            require __DIR__ . '/../Views/pages/cms/media.php';
        });
    }

    public function upload(): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $this->validateMediaCsrf();
            $this->validateFilePresent();
            $this->processMediaUpload();
        });
    }

    public function delete(): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $this->validateMediaCsrf();
            $this->processMediaDelete();
        });
    }

    public function list(): void
    {
        $this->handleCmsJsonRequest(function (): void {
            $allAssets = $this->mediaAssetService->getAllAssets();
            $data = array_map([CmsMediaMapper::class, 'toMediaJsonData'], $allAssets);
            $this->json(['success' => true, 'assets' => $data]);
        });
    }

    private function buildMediaListViewModel(): CmsMediaLibraryViewModel
    {
        $allAssets = $this->mediaAssetService->getAllAssets();
        $assets = array_map([CmsMediaMapper::class, 'toMediaListItemViewModel'], $allAssets);
        return new CmsMediaLibraryViewModel(
            assets: $assets,
            imageLimits: $this->mediaAssetService->getImageLimits(),
            csrfToken: $this->sessionService->getCsrfToken('cms_media'),
            successMessage: $this->sessionService->consumeFlash('success'),
            errorMessage: $this->sessionService->consumeFlash('error'),
        );
    }

    private function validateMediaCsrf(): void
    {
        $csrfToken = $this->readStringPostParam('_csrf') ?? '';
        if (!$this->sessionService->isValidCsrfToken('cms_media', $csrfToken)) {
            throw new ValidationException('Invalid security token');
        }
    }

    private function validateFilePresent(): void
    {
        if (!isset($_FILES['image'])) {
            throw new ValidationException('No file uploaded');
        }
    }

    private function processMediaUpload(): void
    {
        $result = $this->mediaAssetService->uploadImage($_FILES['image'], 'cms');
        $this->json([
            'success' => true,
            'mediaAssetId' => $result->mediaAssetId,
            'filePath' => $result->filePath,
            'originalFileName' => $result->originalFileName,
            'fileSize' => $result->fileSizeBytes,
        ]);
    }

    private function processMediaDelete(): void
    {
        $mediaAssetId = $this->readOptionalIntPostParam('media_asset_id') ?? 0;
        if ($mediaAssetId <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid asset ID']);
            return;
        }
        $deleted = $this->mediaAssetService->deleteAsset($mediaAssetId);
        $this->json(['success' => $deleted]);
    }
}

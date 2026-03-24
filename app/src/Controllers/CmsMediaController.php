<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsMapper;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsMediaLibraryViewModel;

/**
 * CMS controller for the media asset library.
 *
 * Handles browsing, uploading, and deleting image assets used across
 * the festival site. Provides both page-rendered views (index) and JSON
 * endpoints (upload, delete, list) consumed by AJAX pickers in the
 * page editor and event forms.
 *
 * Media assets are context-tagged (currently all "cms") so they can be
 * filtered or scoped in the future.
 */
class CmsMediaController extends CmsBaseController
{
    public function __construct(
        private readonly IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the media library page with all uploaded assets and upload limits.
     * GET /cms/media
     */
    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'media';
            $viewModel = $this->buildMediaListViewModel();
            require __DIR__ . '/../Views/pages/cms/media.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Handles image file upload via AJAX and returns the new asset details as JSON.
     * POST /cms/media/upload
     */
    public function upload(): void
    {
        header('Content-Type: application/json');
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateMediaCsrf();
            $this->validateFilePresent();
            $this->processMediaUpload();
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Upload failed']);
        }
    }

    /**
     * Deletes a media asset by ID via AJAX and returns success/failure as JSON.
     * POST /cms/media/delete
     */
    public function delete(): void
    {
        header('Content-Type: application/json');
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateMediaCsrf();
            $this->processMediaDelete();
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Delete failed']);
        }
    }

    /**
     * Returns all media assets as a JSON array for use by client-side pickers
     * (e.g. the image-selection modal in the page editor).
     * GET /cms/media/list
     */
    public function list(): void
    {
        header('Content-Type: application/json');
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $allAssets = $this->mediaAssetService->getAllAssets();
            // Reuses CmsEventsMapper for JSON serialization — shared DTO shape across media consumers
            $data = array_map([CmsEventsMapper::class, 'toMediaJsonData'], $allAssets);
            echo json_encode(['success' => true, 'assets' => $data]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to load media']);
        }
    }

    private function buildMediaListViewModel(): CmsMediaLibraryViewModel
    {
        $allAssets = $this->mediaAssetService->getAllAssets();
        $assets = array_map([CmsEventsMapper::class, 'toMediaListItemViewModel'], $allAssets);
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
        $csrfToken = $_POST['csrf_token'] ?? '';
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
        echo json_encode([
            'success' => true,
            'mediaAssetId' => $result->mediaAssetId,
            'filePath' => $result->filePath,
            'originalFileName' => $result->originalFileName,
            'fileSize' => $result->fileSizeBytes,
        ]);
    }

    private function processMediaDelete(): void
    {
        $mediaAssetId = (int)($_POST['media_asset_id'] ?? 0);
        if ($mediaAssetId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid asset ID']);
            return;
        }
        $deleted = $this->mediaAssetService->deleteAsset($mediaAssetId);
        echo json_encode(['success' => $deleted]);
    }
}

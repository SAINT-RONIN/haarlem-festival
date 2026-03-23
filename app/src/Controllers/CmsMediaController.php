<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\ValidationException;
use App\Mappers\CmsEventsMapper;
use App\Models\MediaAsset;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsMediaListItemViewModel;

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
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'media';
            $viewModel = $this->buildMediaListViewModel();
            require __DIR__ . '/../Views/pages/cms/media.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

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

    public function list(): void
    {
        header('Content-Type: application/json');
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $allAssets = $this->mediaAssetService->getAllAssets();
            $data = array_map([CmsEventsMapper::class, 'toMediaJsonData'], $allAssets);
            echo json_encode(['success' => true, 'assets' => $data]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to load media']);
        }
    }

    private function buildMediaListViewModel(): \App\ViewModels\Cms\CmsMediaLibraryViewModel
    {
        $allAssets = $this->mediaAssetService->getAllAssets();
        $assets = array_map([CmsEventsMapper::class, 'toMediaListItemViewModel'], $allAssets);
        return CmsEventsMapper::toMediaLibraryViewModel(
            $assets,
            $this->mediaAssetService->getImageLimits(),
            $this->sessionService->getCsrfToken('cms_media'),
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
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

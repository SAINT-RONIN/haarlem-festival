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

class CmsMediaController
{
    public function __construct(
        private readonly IMediaAssetService $mediaAssetService,
        private readonly ISessionService $sessionService,
    ) {
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView = 'media';
            $allAssets = $this->mediaAssetService->getAllAssets();

            $assets = array_map([CmsEventsMapper::class, 'toMediaListItemViewModel'], $allAssets);
            $viewModel = CmsEventsMapper::toMediaLibraryViewModel(
                $assets,
                $this->mediaAssetService->getImageLimits(),
                $this->sessionService->getCsrfToken('cms_media'),
                $_GET['success'] ?? null,
                $_GET['error'] ?? null,
            );

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

            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->sessionService->isValidCsrfToken('cms_media', $csrfToken)) {
                echo json_encode(['success' => false, 'error' => 'Invalid security token']);
                return;
            }

            if (!isset($_FILES['image'])) {
                echo json_encode(['success' => false, 'error' => 'No file uploaded']);
                return;
            }

            $result = $this->mediaAssetService->uploadImage($_FILES['image'], 'cms');

            echo json_encode([
                'success' => true,
                'mediaAssetId' => $result->mediaAssetId,
                'filePath' => $result->filePath,
                'originalFileName' => $result->originalFileName,
                'fileSize' => $result->fileSizeBytes,
            ]);
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

            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!$this->sessionService->isValidCsrfToken('cms_media', $csrfToken)) {
                echo json_encode(['success' => false, 'error' => 'Invalid security token']);
                return;
            }

            $mediaAssetId = (int)($_POST['media_asset_id'] ?? 0);
            if ($mediaAssetId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid asset ID']);
                return;
            }

            $deleted = $this->mediaAssetService->deleteAsset($mediaAssetId);

            echo json_encode(['success' => $deleted]);
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
}

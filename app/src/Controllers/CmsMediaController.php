<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\ValidationException;
use App\Models\MediaAsset;
use App\Repositories\MediaAssetRepository;
use App\Services\MediaAssetService;
use App\Services\SessionService;
use App\ViewModels\Cms\CmsMediaLibraryViewModel;
use App\ViewModels\Cms\CmsMediaListItemViewModel;

class CmsMediaController
{
    private MediaAssetService $mediaAssetService;
    private SessionService $sessionService;

    public function __construct()
    {
        $this->mediaAssetService = new MediaAssetService();
        $this->sessionService = new SessionService();
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin();

            $currentView = 'media';
            $allAssets = (new MediaAssetRepository())->findAll();

            $assets = array_map(
                static fn(MediaAsset $asset): CmsMediaListItemViewModel => CmsMediaListItemViewModel::fromModel($asset),
                $allAssets
            );

            $viewModel = new CmsMediaLibraryViewModel(
                assets: $assets,
                imageLimits: $this->mediaAssetService->getImageLimits(),
                csrfToken: $this->sessionService->getCsrfToken('cms_media'),
                successMessage: $_GET['success'] ?? null,
                errorMessage: $_GET['error'] ?? null,
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
            CmsAuthController::requireAdmin();

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
            CmsAuthController::requireAdmin();

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
            CmsAuthController::requireAdmin();

            $allAssets = (new MediaAssetRepository())->findAll();

            $data = array_map(static fn(MediaAsset $asset): array => [
                'mediaAssetId' => $asset->mediaAssetId,
                'filePath' => $asset->filePath,
                'originalFileName' => $asset->originalFileName,
                'mimeType' => $asset->mimeType,
            ], $allAssets);

            echo json_encode(['success' => true, 'assets' => $data]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to load media']);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\CmsDashboardService;
use App\Services\CmsEditService;
use App\Services\MediaAssetService;
use App\Services\SessionService;
use App\ViewModels\CmsPageEditViewModel;

class CmsDashboardController
{
    private SessionService $sessionService;
    private CmsDashboardService $dashboardService;
    private CmsEditService $cmsEditService;
    private MediaAssetService $mediaAssetService;

    public function __construct()
    {
        $this->sessionService = new SessionService();
        $this->dashboardService = new CmsDashboardService();
        $this->cmsEditService = new CmsEditService();
        $this->mediaAssetService = new MediaAssetService();
    }

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin();
            $currentView = 'dashboard';
            $viewModel = $this->dashboardService->getDashboardData($this->getUserDisplayName());
            require __DIR__ . '/../Views/pages/cms/dashboard.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function pages(): void
    {
        try {
            CmsAuthController::requireAdmin();
            $currentView = 'pages';
            $searchQuery = $_GET['search'] ?? '';
            $viewModel = $this->dashboardService->getPagesListData($searchQuery, $this->getUserDisplayName());
            require __DIR__ . '/../Views/pages/cms/dashboard.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(string $id): void
    {
        try {
            CmsAuthController::requireAdmin();

            $pageId = (int)$id;
            $pageData = $this->cmsEditService->getPageForEditing($pageId);
            if ($pageData === null) {
                throw new NotFoundException('Page', $pageId);
            }

            $viewModel = new CmsPageEditViewModel($pageData);
            $viewData = $viewModel->getViewData();

            $page = $viewData['page'];
            $sections = $viewData['sections'];
            $contentLimits = $viewData['contentLimits'];
            $imageLimits = $viewData['imageLimits'];
            $userName = $this->getUserDisplayName();
            $successMessage = $_SESSION['cms_success'] ?? null;
            $errorMessage = $_SESSION['cms_error'] ?? null;
            unset($_SESSION['cms_success'], $_SESSION['cms_error']);

            require __DIR__ . '/../Views/pages/cms/page-edit.php';
        } catch (NotFoundException $error) {
            http_response_code(404);
            $errorMessage = $error->getMessage();
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function update(string $id): void
    {
        try {
            CmsAuthController::requireAdmin();

            $pageId = (int)$id;
            $items = $_POST['items'] ?? [];
            if ($items === []) {
                $_SESSION['cms_error'] = 'No changes submitted';
                header("Location: /cms/pages/{$pageId}/edit");
                exit;
            }

            $formattedItems = [];
            foreach ($items as $itemId => $value) {
                $formattedItems[$itemId] = ['value' => $value];
            }

            $result = $this->cmsEditService->updatePageItems($pageId, $formattedItems);
            if ($result['success']) {
                $_SESSION['cms_success'] = "Updated {$result['updatedCount']} item(s) successfully";
            } else {
                $_SESSION['cms_error'] = implode(', ', $result['errors']);
            }

            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function uploadImage(string $id): void
    {
        try {
            CmsAuthController::requireAdmin();

            header('Content-Type: application/json');
            $itemId = (int)($_POST['item_id'] ?? 0);

            if ($itemId === 0) {
                echo json_encode(['success' => false, 'error' => 'Missing item ID']);
                return;
            }

            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                echo json_encode(['success' => false, 'error' => 'No file uploaded']);
                return;
            }

            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], 'cms');
            $this->cmsEditService->updateItemImage($itemId, $mediaAsset['MediaAssetId']);

            echo json_encode([
                'success' => true,
                'mediaAssetId' => $mediaAsset['MediaAssetId'],
                'filePath' => $mediaAsset['FilePath'],
                'message' => 'Image uploaded successfully',
            ]);
        } catch (ValidationException $error) {
            echo json_encode(['success' => false, 'error' => $error->getMessage()]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    private function getUserDisplayName(): string
    {
        $this->sessionService->start();
        return $_SESSION['user_display_name'] ?? 'Administrator';
    }
}

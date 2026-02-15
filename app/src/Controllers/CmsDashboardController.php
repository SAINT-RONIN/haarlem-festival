<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\CmsDashboardService;
use App\Services\CmsEditService;
use App\Services\MediaAssetService;
use App\Services\SessionService;
use App\ViewModels\CmsPageEditViewModel;

/**
 * Controller for the CMS Dashboard.
 *
 * Handles the main CMS admin panel views including
 * dashboard overview and pages management.
 * Thin controller - delegates business logic to services.
 */
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

    /**
     * Displays the CMS Dashboard.
     * GET /cms
     */
    public function index(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'dashboard';
        $viewModel = $this->dashboardService->getDashboardData($this->getUserDisplayName());

        require __DIR__ . '/../Views/pages/cms/dashboard.php';
    }

    /**
     * Displays the CMS Pages list.
     * GET /cms/pages
     */
    public function pages(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'pages';
        $searchQuery = $_GET['search'] ?? '';
        $viewModel = $this->dashboardService->getPagesListData($searchQuery, $this->getUserDisplayName());

        require __DIR__ . '/../Views/pages/cms/dashboard.php';
    }

    /**
     * Gets the current user's display name from session.
     */
    private function getUserDisplayName(): string
    {
        $this->sessionService->start();
        return $_SESSION['user_display_name'] ?? 'Administrator';
    }


    /**
     * Displays the page edit form.
     * GET /cms/pages/{id}/edit
     */
    public function edit(string $id): void
    {
        CmsAuthController::requireAdmin();

        try {
            $pageId = (int)$id;
            $pageData = $this->cmsEditService->getPageForEditing($pageId);

            if (!$pageData) {
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
        } catch (NotFoundException $e) {
            http_response_code(404);
            $errorMessage = $e->getMessage();
            require __DIR__ . '/../Views/pages/errors/404.php';
        }
    }

    /**
     * Handles page content update.
     * POST /cms/pages/{id}/edit
     */
    public function update(string $id): void
    {
        CmsAuthController::requireAdmin();

        $pageId = (int)$id;
        $items = $_POST['items'] ?? [];

        if (empty($items)) {
            $_SESSION['cms_error'] = 'No changes submitted';
            header("Location: /cms/pages/edit");
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
    }

    /**
     * Handles image upload via AJAX.
     * POST /cms/pages/{id}/upload-image
     */
    public function uploadImage(string $id): void
    {
        CmsAuthController::requireAdmin();

        header('Content-Type: application/json');

        $pageId = (int)$id;
        $itemId = (int)($_POST['item_id'] ?? 0);

        if (!$itemId) {
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        try {
            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], 'cms');
            $this->cmsEditService->updateItemImage($itemId, $mediaAsset['MediaAssetId']);

            echo json_encode([
                'success' => true,
                'mediaAssetId' => $mediaAsset['MediaAssetId'],
                'filePath' => $mediaAsset['FilePath'],
                'message' => 'Image uploaded successfully'
            ]);
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }
}

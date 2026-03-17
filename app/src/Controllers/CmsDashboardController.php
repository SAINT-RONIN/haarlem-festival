<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Services\CmsDashboardService;
use App\Services\CmsEditService;
use App\Services\MediaAssetService;
use App\Services\SessionService;
use App\ViewModels\CmsPageEditViewModel;

/**
 * Controller for the CMS Dashboard.
 *
 * HTTP orchestration only:
 * - auth checks
 * - service calls
 * - selecting views / redirects / response codes
 */
class CmsDashboardController
{
    private const MEDIA_CONTEXT_CMS = 'cms';
    private const CSRF_SCOPE_PAGE_EDIT = 'cms_page_edit';

    public function __construct(
        private readonly SessionService $sessionService,
        private readonly CmsDashboardService $cmsDashboardService,
        private readonly CmsEditService $cmsEditService,
        private readonly MediaAssetService $mediaAssetService,
    ) {
    }

    /**
     * Displays the CMS Dashboard.
     * GET /cms
     */
    public function index(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'dashboard';
        $viewModel = $this->cmsDashboardService->getDashboardData($this->getUserDisplayName());

        $this->render(__DIR__ . '/../Views/pages/cms/dashboard.php', [
            'currentView' => $currentView,
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * Displays the CMS Pages list.
     * GET /cms/pages
     */
    public function pages(): void
    {
        CmsAuthController::requireAdmin();

        $currentView = 'pages';
        $searchQuery = trim((string)filter_input(INPUT_GET, 'search'));
        $viewModel = $this->cmsDashboardService->getPagesListData($searchQuery, $this->getUserDisplayName());

        $this->render(__DIR__ . '/../Views/pages/cms/dashboard.php', [
            'currentView' => $currentView,
            'searchQuery' => $searchQuery,
            'viewModel' => $viewModel,
        ]);
    }

    /**
     * Displays the page edit form.
     * GET /cms/pages/{id}/edit
     */
    public function edit(string $id): void
    {
        CmsAuthController::requireAdmin();

        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            http_response_code(400);
            echo 'Invalid page ID';
            return;
        }

        $pageData = $this->cmsEditService->getPageForEditing($pageId);
        if ($pageData === null) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        $viewData = (new CmsPageEditViewModel($pageData))->getViewData();
        $page = $viewData['page'];
        $sections = $viewData['sections'];

        $this->sessionService->start();
        $csrfToken = $this->sessionService->getCsrfToken(self::CSRF_SCOPE_PAGE_EDIT);

        $this->render(__DIR__ . '/../Views/pages/cms/page-edit.php', [
            'page' => $page,
            'sections' => $sections,
            'previewUrl' => $this->cmsEditService->resolvePreviewUrl($page, $sections),
            'contentLimits' => $viewData['contentLimits'],
            'imageLimits' => $viewData['imageLimits'],
            'userName' => $this->getUserDisplayName(),
            'successMessage' => $this->sessionService->consumeFlash('cms_success'),
            'errorMessage' => $this->sessionService->consumeFlash('cms_error'),
            'csrfToken' => $csrfToken,
        ]);
    }

    /**
     * Handles page content update.
     * POST /cms/pages/{id}/edit
     */
    public function update(string $id): void
    {
        CmsAuthController::requireAdmin();

        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            http_response_code(400);
            echo 'Invalid page ID';
            return;
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_PAGE_EDIT, is_string($csrfToken) ? $csrfToken : null)) {
            $this->sessionService->setFlash('cms_error', 'Invalid request token. Please refresh and try again.');
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }

        $items = $_POST['items'] ?? [];
        if (!is_array($items) || $items === []) {
            $this->sessionService->setFlash('cms_error', 'No changes submitted');
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }

        $formattedItems = [];
        foreach ($items as $itemId => $value) {
            $formattedItems[$itemId] = ['value' => $value];
        }

        $result = $this->cmsEditService->updatePageItems($pageId, $formattedItems);

        if (($result['success'] ?? false) === true) {
            $updatedCount = (int)($result['updatedCount'] ?? 0);
            $this->sessionService->setFlash('cms_success', "Updated {$updatedCount} item(s) successfully");
        } else {
            $errors = $result['errors'] ?? [];
            $this->sessionService->setFlash('cms_error', is_array($errors) ? implode(', ', $errors) : 'Failed to update content');
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

        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            echo json_encode(['success' => false, 'error' => 'Invalid page ID']);
            return;
        }

        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_PAGE_EDIT, is_string($csrfToken) ? $csrfToken : null)) {
            echo json_encode(['success' => false, 'error' => 'Invalid request token']);
            return;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Missing item ID']);
            return;
        }

        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        try {
            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], self::MEDIA_CONTEXT_CMS);
            $this->cmsEditService->updateItemImage($itemId, (int)$mediaAsset['MediaAssetId']);

            echo json_encode([
                'success' => true,
                'mediaAssetId' => $mediaAsset['MediaAssetId'],
                'filePath' => $mediaAsset['FilePath'],
                'message' => 'Image uploaded successfully',
            ]);
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'error' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    private function getUserDisplayName(): string
    {
        $name = $this->sessionService->get('user_display_name', 'Administrator');
        return is_string($name) && $name !== '' ? $name : 'Administrator';
    }

    private function parsePositiveIntId(string $id): ?int
    {
        if ($id === '' || ctype_digit($id) === false) {
            return null;
        }

        $value = (int)$id;
        return $value > 0 ? $value : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $viewPath, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require $viewPath;
    }
}

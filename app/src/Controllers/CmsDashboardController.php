<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\CmsMessages;
use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\CmsEditException;
use App\Exceptions\ValidationException;
use App\Mappers\CmsDashboardMapper;
use App\Services\Interfaces\ICmsDashboardService;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsPageEditViewModel;

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
        private readonly ISessionService $sessionService,
        private readonly ICmsDashboardService $cmsDashboardService,
        private readonly ICmsEditService $cmsEditService,
        private readonly IMediaAssetService $mediaAssetService,
    ) {
    }

    /**
     * Displays the CMS Dashboard.
     * GET /cms
     */
    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView = 'dashboard';
            $domainData = $this->cmsDashboardService->getDashboardData();
            $viewModel = CmsDashboardMapper::toDashboardViewModel(
                $domainData->recentPages,
                $domainData->activities,
                $this->getUserDisplayName(),
            );

            $this->render(__DIR__ . '/../Views/pages/cms/dashboard.php', [
                'currentView' => $currentView,
                'viewModel' => $viewModel,
            ]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays the CMS Pages list.
     * GET /cms/pages
     */
    public function pages(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);

            $currentView = 'pages';
            $searchQuery = trim((string)filter_input(INPUT_GET, 'search'));
            $allPages = $this->cmsDashboardService->getPagesListData();
            $viewModel = CmsDashboardMapper::toPagesListViewModel($allPages, $searchQuery, $this->getUserDisplayName());

            $this->render(__DIR__ . '/../Views/pages/cms/dashboard.php', [
                'currentView' => $currentView,
                'searchQuery' => $searchQuery,
                'viewModel' => $viewModel,
            ]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Displays the page edit form.
     * GET /cms/pages/{id}/edit
     */
    public function edit(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $pageId = $this->requireValidPageId($id);
            if ($pageId === null) {
                return;
            }
            $this->renderEditPage($pageId);
        } catch (CmsEditException $e) {
            http_response_code(500);
            require __DIR__ . '/../Views/pages/errors/500.php';
        }
    }

    private function renderEditPage(int $pageId): void
    {
        $pageData = $this->cmsEditService->getPageForEditing($pageId);
        if ($pageData === null) {
            http_response_code(404);
            echo CmsMessages::PAGE_NOT_FOUND;
            return;
        }

        $previewUrl = $this->cmsEditService->resolvePreviewUrl($pageData->page, $pageData->sections);
        $viewData = CmsDashboardMapper::toPageEditViewData($pageData);
        $this->sessionService->start();
        $csrfToken = $this->sessionService->getCsrfToken(self::CSRF_SCOPE_PAGE_EDIT);

        $this->render(__DIR__ . '/../Views/pages/cms/page-edit.php', $this->buildEditRenderData($viewData, $previewUrl, $csrfToken));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEditRenderData(CmsPageEditViewModel $viewData, string $previewUrl, string $csrfToken): array
    {
        return array_merge(
            $this->buildEditViewModelFields($viewData, $previewUrl),
            $this->buildEditSessionFields($csrfToken),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEditViewModelFields(CmsPageEditViewModel $viewData, string $previewUrl): array
    {
        return [
            'page'          => $viewData->page,
            'sections'      => $viewData->sections,
            'previewUrl'    => $previewUrl,
            'contentLimits' => $viewData->contentLimits,
            'imageLimits'   => $viewData->imageLimits,
            'userName'      => $this->getUserDisplayName(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildEditSessionFields(string $csrfToken): array
    {
        return [
            'successMessage' => $this->sessionService->consumeFlash('cms_success'),
            'errorMessage'   => $this->sessionService->consumeFlash('cms_error'),
            'csrfToken'      => $csrfToken,
        ];
    }

    /**
     * Handles page content update.
     * POST /cms/pages/{id}/edit
     */
    public function update(string $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $pageId = $this->requireValidPageId($id);
            if ($pageId === null) {
                return;
            }
            $this->validateCsrfOrRedirect($pageId);
            $this->validateItemsOrRedirect($pageId);
            $this->performUpdateAndRedirect($pageId);
        } catch (CmsEditException $e) {
            $this->handleUpdateError($e, $this->parsePositiveIntId($id));
        }
    }

    private function handleUpdateError(CmsEditException $e, ?int $pageId): void
    {
        $this->sessionService->setFlash('cms_error', CmsMessages::UPDATE_UNEXPECTED_ERROR);
        header($pageId !== null ? "Location: /cms/pages/{$pageId}/edit" : 'Location: /cms/pages');
        exit;
    }

    private function validateCsrfOrRedirect(int $pageId): void
    {
        $csrfToken = $_POST['csrf_token'] ?? null;
        $isValid = $this->sessionService->isValidCsrfToken(
            self::CSRF_SCOPE_PAGE_EDIT,
            is_string($csrfToken) ? $csrfToken : null,
        );
        if (!$isValid) {
            $this->sessionService->setFlash('cms_error', CmsMessages::INVALID_CSRF);
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }
    }

    private function validateItemsOrRedirect(int $pageId): void
    {
        $items = $_POST['items'] ?? [];
        if (!is_array($items) || $items === []) {
            $this->sessionService->setFlash('cms_error', CmsMessages::NO_CHANGES);
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }
    }

    private function performUpdateAndRedirect(int $pageId): void
    {
        $result = $this->cmsEditService->updatePageItems($pageId, $_POST['items']);

        if ($result->success) {
            $this->sessionService->setFlash('cms_success', sprintf(CmsMessages::UPDATE_SUCCESS_TEMPLATE, $result->updatedCount));
        } else {
            $errorMessage = $result->errors !== [] ? implode(', ', $result->errors) : CmsMessages::UPDATE_FAILED;
            $this->sessionService->setFlash('cms_error', $errorMessage);
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
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            header('Content-Type: application/json');
            $pageId = $this->requireValidPageIdJson($id);
            if ($pageId === null) {
                return;
            }
            $this->processUploadRequest();
        } catch (CmsEditException $e) {
            echo json_encode(['success' => false, 'error' => CmsMessages::UPDATE_UNEXPECTED_ERROR]);
        }
    }

    private function isUploadRequestValid(): bool
    {
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_PAGE_EDIT, is_string($csrfToken) ? $csrfToken : null)) {
            echo json_encode(['success' => false, 'error' => CmsMessages::INVALID_CSRF]);
            return false;
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId <= 0) {
            echo json_encode(['success' => false, 'error' => CmsMessages::MISSING_ITEM_ID]);
            return false;
        }

        return true;
    }

    private function dispatchUploadAction(): void
    {
        $itemId = (int)($_POST['item_id'] ?? 0);
        $mediaAssetId = (int)($_POST['media_asset_id'] ?? 0);

        if ($mediaAssetId > 0) {
            $this->handleExistingAssetLink($itemId, $mediaAssetId);
            return;
        }

        $this->handleNewFileUpload($itemId);
    }

    private function handleExistingAssetLink(int $itemId, int $mediaAssetId): void
    {
        try {
            $this->cmsEditService->updateItemImage($itemId, $mediaAssetId);
            $asset = $this->mediaAssetService->getAssetById($mediaAssetId);
            echo json_encode($this->buildLinkedAssetResponse($mediaAssetId, $asset?->filePath ?? ''));
        } catch (CmsEditException $e) {
            echo json_encode(['success' => false, 'error' => CmsMessages::IMAGE_LINK_FAILED]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLinkedAssetResponse(int $mediaAssetId, string $filePath): array
    {
        return ['success' => true, 'mediaAssetId' => $mediaAssetId, 'filePath' => $filePath, 'message' => CmsMessages::IMAGE_LINK_SUCCESS];
    }

    private function handleNewFileUpload(int $itemId): void
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => CmsMessages::NO_FILE_UPLOADED]);
            return;
        }

        $this->uploadAndLinkImage($itemId);
    }

    private function uploadAndLinkImage(int $itemId): void
    {
        try {
            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], self::MEDIA_CONTEXT_CMS);
            $this->cmsEditService->updateItemImage($itemId, $mediaAsset->mediaAssetId);
            echo json_encode(['success' => true, 'mediaAssetId' => $mediaAsset->mediaAssetId, 'filePath' => $mediaAsset->filePath, 'message' => CmsMessages::IMAGE_UPLOAD_SUCCESS]);
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function getUserDisplayName(): string
    {
        $name = $this->sessionService->get('user_display_name', CmsMessages::DEFAULT_ADMIN_NAME);
        return is_string($name) && $name !== '' ? $name : CmsMessages::DEFAULT_ADMIN_NAME;
    }

    private function requireValidPageId(string $id): ?int
    {
        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            http_response_code(400);
            echo CmsMessages::INVALID_PAGE_ID;
        }
        return $pageId;
    }

    private function requireValidPageIdJson(string $id): ?int
    {
        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            echo json_encode(['success' => false, 'error' => CmsMessages::INVALID_PAGE_ID]);
        }
        return $pageId;
    }

    private function processUploadRequest(): void
    {
        if (!$this->isUploadRequestValid()) {
            return;
        }
        $this->dispatchUploadAction();
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

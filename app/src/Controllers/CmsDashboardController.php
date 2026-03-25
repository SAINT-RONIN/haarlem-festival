<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\CmsMessages;
use App\Controllers\Support\ControllerErrorResponder;
use App\Exceptions\CmsEditException;
use App\Exceptions\ValidationException;
use App\Mappers\CmsDashboardViewMapper;
use App\Services\Interfaces\ICmsDashboardService;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsPageEditViewModel;

/**
 * Controller for the CMS Dashboard, page listing, and inline page editor.
 *
 * Manages three main flows:
 * 1. Dashboard overview — recent pages and activity log.
 * 2. Pages list — searchable CMS page inventory.
 * 3. Page edit — section-level content editing with AJAX image upload.
 *
 * Image uploads support two modes: linking an existing media-library asset
 * or uploading a new file (which creates the asset and links it in one step).
 *
 * Uses extract() for view rendering to keep view files decoupled from
 * controller internals (variables appear as locals in the template).
 */
class CmsDashboardController extends CmsBaseController
{
    private const MEDIA_CONTEXT_CMS = 'cms';
    private const CSRF_SCOPE_PAGE_EDIT = 'cms_page_edit';

    public function __construct(
        ISessionService $sessionService,
        private readonly ICmsDashboardService $cmsDashboardService,
        private readonly ICmsEditService $cmsEditService,
        private readonly IMediaAssetService $mediaAssetService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the CMS Dashboard.
     * GET /cms
     */
    public function index(): void
    {
        try {

            $currentView = 'dashboard';
            $domainData = $this->cmsDashboardService->getDashboardData();
            $viewModel = CmsDashboardViewMapper::toDashboardViewModel(
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

            $currentView = 'pages';
            $searchQuery = trim((string)filter_input(INPUT_GET, 'search'));
            $allPages = $this->cmsDashboardService->getPagesListData();
            $viewModel = CmsDashboardViewMapper::toPagesListViewModel($allPages, $searchQuery, $this->getUserDisplayName());

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
            $pageId = $this->parsePageId($id);
            if ($pageId === null) {
                return;
            }
            $this->renderEditPage($pageId);
        } catch (CmsEditException $e) {
            http_response_code(500);
            require __DIR__ . '/../Views/pages/errors/500.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /** Loads page data and renders the full edit view, or returns 404 if the page does not exist. */
    private function renderEditPage(int $pageId): void
    {
        $pageData = $this->cmsEditService->getPageForEditing($pageId);
        if ($pageData === null) {
            http_response_code(404);
            echo CmsMessages::PAGE_NOT_FOUND;
            return;
        }

        $previewUrl = $this->cmsEditService->resolvePreviewUrl($pageData->page, $pageData->sections);
        $viewData = CmsDashboardViewMapper::toPageEditViewData($pageData);
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
     * Handles page content update via a three-step validation pipeline:
     * CSRF check -> items presence check -> persist and redirect.
     * Each step short-circuits with a redirect if validation fails.
     * POST /cms/pages/{id}/edit
     *
     * @throws CmsEditException Caught internally; redirects with error flash.
     */
    public function update(string $id): void
    {
        try {
            $pageId = $this->parsePageId($id);
            if ($pageId === null) {
                return;
            }
            $this->validateCsrfOrRedirect($pageId);
            $this->validateItemsOrRedirect($pageId);
            $this->performUpdateAndRedirect($pageId);
        } catch (CmsEditException $e) {
            $this->handleUpdateError($e, $this->parsePositiveIntId($id));
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function handleUpdateError(CmsEditException $e, ?int $pageId): void
    {
        $this->sessionService->setFlash('cms_error', CmsMessages::UPDATE_UNEXPECTED_ERROR);
        header($pageId !== null ? "Location: /cms/pages/{$pageId}/edit" : 'Location: /cms/pages');
        exit;
    }

    /** Validates the CSRF token from POST; redirects back to the edit page if invalid. */
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

    /** Ensures the submitted items array is non-empty; redirects if there is nothing to update. */
    private function validateItemsOrRedirect(int $pageId): void
    {
        $items = $_POST['items'] ?? [];
        if (!is_array($items) || $items === []) {
            $this->sessionService->setFlash('cms_error', CmsMessages::NO_CHANGES);
            header("Location: /cms/pages/{$pageId}/edit");
            exit;
        }
    }

    /** Persists the page item changes and redirects with a success or error flash. */
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
     * Handles image upload via AJAX for the page editor's image sections.
     * Supports two flows: linking an existing media-library asset by ID,
     * or uploading a brand-new file (multipart form data).
     * POST /cms/pages/{id}/upload-image
     *
     * @throws CmsEditException Caught internally; returns JSON error.
     */
    public function uploadImage(string $id): void
    {
        try {
            header('Content-Type: application/json');
            $pageId = $this->parsePageIdJson($id);
            if ($pageId === null) {
                return;
            }
            $this->processUploadRequest();
        } catch (CmsEditException $e) {
            echo json_encode(['success' => false, 'error' => CmsMessages::UPDATE_UNEXPECTED_ERROR]);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respondJson($error);
        }
    }

    /** Routes the upload request to either link an existing asset or handle a new file upload. */
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

    /**
     * Handles TinyMCE inline image uploads where no CMS item needs linking.
     * Uploads the file and returns the URL without updating any content item.
     */
    private function handleTinyMceUpload(): void
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => CmsMessages::NO_FILE_UPLOADED]);
            return;
        }

        try {
            $mediaAsset = $this->mediaAssetService->uploadImage($_FILES['image'], self::MEDIA_CONTEXT_CMS);
            echo json_encode(['success' => true, 'filePath' => $mediaAsset->filePath]);
        } catch (ValidationException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /** Links an already-uploaded media asset to a page content item. */
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

    /** Validates that a file was submitted, then delegates to the upload-and-link flow. */
    private function handleNewFileUpload(int $itemId): void
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'error' => CmsMessages::NO_FILE_UPLOADED]);
            return;
        }

        $this->uploadAndLinkImage($itemId);
    }

    /** Uploads the file to storage, creates a media asset record, and links it to the content item. */
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

    private function parsePageId(string $id): ?int
    {
        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            http_response_code(400);
            echo CmsMessages::INVALID_PAGE_ID;
        }
        return $pageId;
    }

    private function parsePageIdJson(string $id): ?int
    {
        $pageId = $this->parsePositiveIntId($id);
        if ($pageId === null) {
            echo json_encode(['success' => false, 'error' => CmsMessages::INVALID_PAGE_ID]);
        }
        return $pageId;
    }

    private function processUploadRequest(): void
    {
        // Validate CSRF token first for all upload types
        $csrfToken = $_POST['csrf_token'] ?? null;
        if (!$this->sessionService->isValidCsrfToken(self::CSRF_SCOPE_PAGE_EDIT, is_string($csrfToken) ? $csrfToken : null)) {
            echo json_encode(['success' => false, 'error' => CmsMessages::INVALID_CSRF]);
            return;
        }

        // TinyMCE inline uploads send item_id=0 — upload only, no CMS item linking
        $itemId = (int)($_POST['item_id'] ?? 0);
        if ($itemId <= 0) {
            $this->handleTinyMceUpload();
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

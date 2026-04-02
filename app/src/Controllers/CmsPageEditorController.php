<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\CmsMessages;
use App\Controllers\Support\ControllerErrorResponder;
use App\DTOs\Cms\CmsPageEditData;
use App\DTOs\Cms\CmsUpdateResult;
use App\Exceptions\CmsEditException;
use App\Mappers\CmsDashboardViewMapper;
use App\Services\Interfaces\ICmsEditService;
use App\Services\Interfaces\ISessionService;
use App\View\ViewRenderer;
use App\ViewModels\Cms\CmsPageEditViewModel;

class CmsPageEditorController extends CmsBaseController
{
    private const CSRF_SCOPE = 'cms_page_edit';
    private const VIEW = __DIR__ . '/../Views/pages/cms/page-edit.php';

    public function __construct(
        ISessionService $sessionService,
        private readonly ICmsEditService $cmsEditService,
    ) {
        parent::__construct($sessionService);
    }

    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->renderEditor($id);
        });
    }

    public function update(int $id): void
    {
        try {
            $this->processUpdate($id);
        } catch (\Throwable $error) {
            $this->handleUpdateError($error, $id);
        }
    }

    private function renderEditor(int $pageId): void
    {
        $pageData = $this->cmsEditService->getPageForEditing($pageId);
        if ($pageData === null) {
            $this->renderMissingPage();
            return;
        }
        ViewRenderer::render(self::VIEW, $this->buildEditViewData($pageData));
    }

    private function buildEditViewData(CmsPageEditData $pageData): array
    {
        $viewData = CmsDashboardViewMapper::toPageEditViewData($pageData);
        $previewUrl = $this->cmsEditService->resolvePreviewUrl($pageData->page, $pageData->sections);
        return array_merge($this->viewFields($viewData, $previewUrl), $this->sessionFields());
    }

    private function viewFields(CmsPageEditViewModel $viewData, string $previewUrl): array
    {
        return ['page' => $viewData->page, 'sections' => $viewData->sections, 'previewUrl' => $previewUrl, 'contentLimits' => $viewData->contentLimits, 'imageLimits' => $viewData->imageLimits, 'userName' => $this->userName()];
    }

    private function sessionFields(): array
    {
        return ['successMessage' => $this->sessionService->consumeFlash('cms_success'), 'errorMessage' => $this->sessionService->consumeFlash('cms_error'), 'csrfToken' => $this->sessionService->getCsrfToken(self::CSRF_SCOPE)];
    }

    private function renderMissingPage(): void
    {
        http_response_code(404);
        echo CmsMessages::PAGE_NOT_FOUND;
    }

    private function processUpdate(int $pageId): void
    {
        $this->validateEditorCsrf($pageId);
        $items = $this->requireSubmittedItems($pageId);
        $this->redirectForUpdateResult($pageId, $this->cmsEditService->updatePageItems($pageId, $items));
    }

    private function validateEditorCsrf(int $pageId): void
    {
        if ($this->sessionService->isValidCsrfToken(self::CSRF_SCOPE, $this->readStringPostParam('_csrf'))) {
            return;
        }
        $this->redirectWithFlash(CmsMessages::INVALID_CSRF, 'cms_error', $this->editUrl($pageId));
    }

    private function requireSubmittedItems(int $pageId): array
    {
        $items = $_POST['items'] ?? [];
        if (is_array($items) && $items !== []) {
            return $items;
        }
        $this->redirectWithFlash(CmsMessages::NO_CHANGES, 'cms_error', $this->editUrl($pageId));
    }

    private function redirectForUpdateResult(int $pageId, CmsUpdateResult $result): never
    {
        if ($result->success) {
            $this->redirectWithFlash(sprintf(CmsMessages::UPDATE_SUCCESS_TEMPLATE, $result->updatedCount), 'cms_success', $this->editUrl($pageId));
        }
        $this->redirectWithFlash($this->updateErrorMessage($result), 'cms_error', $this->editUrl($pageId));
    }

    private function updateErrorMessage(CmsUpdateResult $result): string
    {
        return $result->errors !== [] ? implode(', ', $result->errors) : CmsMessages::UPDATE_FAILED;
    }

    private function handleUpdateError(\Throwable $error, int $pageId): void
    {
        if ($error instanceof CmsEditException) {
            $this->redirectWithFlash(CmsMessages::UPDATE_UNEXPECTED_ERROR, 'cms_error', $this->editUrl($pageId));
        }
        ControllerErrorResponder::respond($error);
    }

    private function editUrl(int $pageId): string
    {
        return "/cms/pages/{$pageId}/edit";
    }

    private function userName(): string
    {
        $name = $this->sessionService->get('user_display_name', CmsMessages::DEFAULT_ADMIN_NAME);
        return is_string($name) && $name !== '' ? $name : CmsMessages::DEFAULT_ADMIN_NAME;
    }
}

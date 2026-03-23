<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsArtistsMapper;
use App\Models\ArtistUpsertData;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ISessionService;

class CmsArtistsController
{
    public function __construct(
        private readonly ICmsArtistsService $artistsService,
        private readonly ISessionService $sessionService,
    ) {}

    public function index(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'artists';
            $search      = trim($_GET['search'] ?? '');
            $artists     = $this->artistsService->getArtists($search ?: null);
            $viewModel   = CmsArtistsMapper::toListViewModel(
                $artists, $search,
                $this->sessionService->consumeFlash('success'),
                $this->sessionService->consumeFlash('error'),
                $this->sessionService->getCsrfToken('cms_artist_delete'),
            );
            require __DIR__ . '/../Views/pages/cms/artists.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function create(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $currentView = 'artists';
            $viewModel   = $this->buildFormViewModel(null, new ArtistUpsertData('', '', '', null, true), []);
            require __DIR__ . '/../Views/pages/cms/artist-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function store(): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_artist_create', '/cms/artists/create');
            $data   = $this->extractFormData();
            $errors = $this->artistsService->validateForCreate($data);
            if (!empty($errors)) {
                $this->renderCreateForm($data, $errors);
                return;
            }
            $this->artistsService->createArtist($data);
            $this->redirectWithFlash('Artist created successfully.', 'success', '/cms/artists');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function edit(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $artist = $this->artistsService->findById($id);
            if ($artist === null) {
                http_response_code(404);
                require __DIR__ . '/../Views/pages/errors/404.php';
                return;
            }
            $currentView = 'artists';
            $viewModel   = $this->buildFormViewModel($id, CmsArtistsMapper::fromArtist($artist), []);
            require __DIR__ . '/../Views/pages/cms/artist-edit.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function update(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_artist_edit_' . $id, '/cms/artists/' . $id . '/edit');
            $data   = $this->extractFormData();
            $errors = $this->artistsService->validateForUpdate($id, $data);
            if (!empty($errors)) {
                $this->renderEditForm($id, $data, $errors);
                return;
            }
            $this->artistsService->updateArtist($id, $data);
            $this->redirectWithFlash('Artist updated successfully.', 'success', '/cms/artists');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    public function delete(int $id): void
    {
        try {
            CmsAuthController::requireAdmin($this->sessionService);
            $this->validateCsrf('cms_artist_delete', '/cms/artists');
            $this->artistsService->deleteArtist($id);
            $this->redirectWithFlash('Artist deactivated successfully.', 'success', '/cms/artists');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function validateCsrf(string $scope, string $redirectUrl): void
    {
        if (!$this->sessionService->isValidCsrfToken($scope, $_POST['_csrf'] ?? null)) {
            $this->sessionService->setFlash('error', 'Invalid CSRF token. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    private function redirectWithFlash(string $message, string $type, string $url): void
    {
        $this->sessionService->setFlash($type, $message);
        header('Location: ' . $url);
        exit;
    }

    private function extractFormData(): ArtistUpsertData
    {
        return new ArtistUpsertData(
            name: trim($_POST['name'] ?? ''),
            style: trim($_POST['style'] ?? ''),
            bioHtml: $_POST['bioHtml'] ?? '',
            imageAssetId: isset($_POST['imageAssetId']) && is_numeric($_POST['imageAssetId']) ? (int) $_POST['imageAssetId'] : null,
            isActive: isset($_POST['isActive']) && $_POST['isActive'] === '1',
        );
    }

    /** @param array<string, string> $errors */
    private function buildFormViewModel(?int $artistId, ArtistUpsertData $data, array $errors): \App\ViewModels\Cms\CmsArtistFormViewModel
    {
        $scope  = $artistId === null ? 'cms_artist_create' : 'cms_artist_edit_' . $artistId;
        $action = $artistId === null ? '/cms/artists' : '/cms/artists/' . $artistId . '/edit';
        $title  = $artistId === null ? 'Create Artist' : 'Edit Artist';
        return CmsArtistsMapper::toFormViewModel($artistId, $data, $this->sessionService->getCsrfToken($scope), $action, $title, $errors);
    }

    /** @param array<string, string> $errors */
    private function renderCreateForm(ArtistUpsertData $data, array $errors): void
    {
        $currentView = 'artists';
        $viewModel   = $this->buildFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/artist-create.php';
    }

    /** @param array<string, string> $errors */
    private function renderEditForm(int $id, ArtistUpsertData $data, array $errors): void
    {
        $currentView = 'artists';
        $viewModel   = $this->buildFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/artist-edit.php';
    }
}

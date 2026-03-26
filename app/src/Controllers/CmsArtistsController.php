<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsArtistsMapper;
use App\DTOs\Cms\ArtistUpsertData;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for managing festival artists.
 *
 * Handles listing, creating, editing, and soft-deleting artist records
 * through the admin panel. All mutations delegate to ICmsArtistsService
 * for validation and persistence; this controller owns only HTTP flow
 * (auth gating, CSRF checks, form re-rendering on validation failure,
 * and flash-message redirects via PRG pattern).
 */
class CmsArtistsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsArtistsService $artistsService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Displays the paginated artist list with optional search filtering.
     * GET /cms/artists
     *
     * @throws \Throwable Caught internally; rendered via ControllerErrorResponder.
     */
    public function index(): void
    {
        try {
            $currentView = 'artists';
            $search      = $this->readStringQueryParam('search');
            $viewModel   = $this->buildArtistsListViewModel($search);
            require __DIR__ . '/../Views/pages/cms/artists.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the blank artist creation form.
     * GET /cms/artists/create
     */
    public function create(): void
    {
        try {
            $currentView = 'artists';
            $viewModel   = $this->buildFormViewModel(null, new ArtistUpsertData('', '', '', null, true), []);
            require __DIR__ . '/../Views/pages/cms/artist-create.php';
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates and persists a new artist from the creation form.
     * POST /cms/artists
     */
    public function store(): void
    {
        try {
            $this->processArtistStore();
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Renders the edit form for an existing artist, pre-filled with current data.
     * GET /cms/artists/{id}/edit
     */
    public function edit(int $id): void
    {
        try {
            $this->renderArtistEditPage($id);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Validates and applies updates to an existing artist.
     * POST /cms/artists/{id}/edit
     */
    public function update(int $id): void
    {
        try {
            $this->processArtistUpdate($id);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Soft-deletes (deactivates) an artist by ID.
     * POST /cms/artists/{id}/delete
     *
     * Uses a single shared CSRF scope for all delete actions on the list page,
     * because the token is generated once for the whole list rather than per row.
     */
    public function delete(int $id): void
    {
        try {
            $this->validateCsrf('cms_artist_delete', '/cms/artists');
            $this->artistsService->deleteArtist($id);
            $this->redirectWithFlash('Artist deactivated successfully.', 'success', '/cms/artists');
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /** Fetches artists from the service and maps them to the list ViewModel. */
    private function buildArtistsListViewModel(?string $search): \App\ViewModels\Cms\CmsArtistsListViewModel
    {
        $artists = $this->artistsService->getArtists($search);
        return CmsArtistsMapper::toListViewModel(
            $artists,
            $search ?? '',
            $this->sessionService->consumeFlash('success'),
            $this->sessionService->consumeFlash('error'),
            $this->sessionService->getCsrfToken('cms_artist_delete'),
        );
    }

    /** Handles CSRF validation, form extraction, validation, and persistence for a new artist. */
    private function processArtistStore(): void
    {
        $this->validateCsrf('cms_artist_create', '/cms/artists/create');
        $data   = $this->extractFormData();
        // Re-render the form with errors if validation fails
        $errors = $this->artistsService->validateForCreate($data);
        if (!empty($errors)) {
            $this->renderCreateForm($data, $errors);
            return;
        }
        $this->artistsService->createArtist($data);
        $this->redirectWithFlash('Artist created successfully.', 'success', '/cms/artists');
    }

    /** Loads the artist by ID, renders 404 if missing, otherwise renders the edit form. */
    private function renderArtistEditPage(int $id): void
    {
        $artist = $this->artistsService->findById($id);
        if ($artist === null) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
            return;
        }
        $currentView = 'artists';
        $viewModel   = $this->buildFormViewModel($id, CmsArtistsMapper::fromArtist($artist), []);
        require __DIR__ . '/../Views/pages/cms/artist-edit.php';
    }

    /** Handles CSRF validation, form extraction, validation, and persistence for updating an artist. */
    private function processArtistUpdate(int $id): void
    {
        $this->validateCsrf('cms_artist_edit_' . $id, '/cms/artists/' . $id . '/edit');
        $data   = $this->extractFormData();
        // Re-render the form with errors if validation fails
        $errors = $this->artistsService->validateForUpdate($id, $data);
        if (!empty($errors)) {
            $this->renderEditForm($id, $data, $errors);
            return;
        }
        $this->artistsService->updateArtist($id, $data);
        $this->redirectWithFlash('Artist updated successfully.', 'success', '/cms/artists');
    }

    private function extractFormData(): ArtistUpsertData
    {
        return new ArtistUpsertData(
            name: $this->readStringPostParam('name') ?? '',
            style: $this->readStringPostParam('style') ?? '',
            bioHtml: $_POST['bioHtml'] ?? '',
            imageAssetId: $this->readOptionalIntPostParam('imageAssetId'),
            isActive: $this->readBoolPostParam('isActive'),
        );
    }

    /**
     * Builds a shared form view-model for both create and edit, switching CSRF scope,
     * form action URL, and page title based on whether an artist ID is present.
     *
     * @param array<string, string> $errors
     */
    private function buildFormViewModel(?int $artistId, ArtistUpsertData $data, array $errors): \App\ViewModels\Cms\CmsArtistFormViewModel
    {
        // null artistId = create mode; non-null = edit mode — drives scope, action, and title
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

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Cms\ArtistUpsertData;
use App\Mappers\CmsArtistsMapper;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ISessionService;

/**
 * CMS controller for managing festival artists (biography, gallery, social links).
 *
 * Lightweight Jazz lineup cards are managed by CmsJazzCardsController.
 * Use addToJazzOverview/removeFromJazzOverview to toggle an artist on the Jazz overview.
 */
class CmsArtistsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsArtistsService $artistsService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'artists';
            $search      = $this->readStringQueryParam('search');
            $viewModel   = $this->buildArtistsListViewModel($search);
            require __DIR__ . '/../Views/pages/cms/artists.php';
        });
    }

    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'artists';
            $viewModel   = $this->buildFormViewModel(null, $this->buildCreateDefaults(), []);
            require __DIR__ . '/../Views/pages/cms/artist-create.php';
        });
    }

    public function store(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->processArtistStore();
        });
    }

    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->renderArtistEditPage($id);
        });
    }

    public function update(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->processArtistUpdate($id);
        });
    }

    public function addToJazzOverview(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $returnTo = $this->readSafeReturnTo('/cms/artists');
            $this->validateCsrf('cms_artist_jazz_overview_add', $returnTo);
            $this->artistsService->setJazzOverviewVisibility($id, true);
            $this->redirectWithFlash('Artist added to the Jazz lineup section.', 'success', $returnTo);
        });
    }

    public function removeFromJazzOverview(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $returnTo = $this->readSafeReturnTo('/cms/artists');
            $this->validateCsrf('cms_artist_jazz_overview_remove', $returnTo);
            $this->artistsService->setJazzOverviewVisibility($id, false);
            $this->redirectWithFlash('Artist removed from the Jazz lineup section.', 'success', $returnTo);
        });
    }

    // Uses a shared CSRF scope because the token is generated once for the whole list, not per row.
    public function delete(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_artist_delete', '/cms/artists');
            $this->artistsService->deleteArtist($id);
            $this->redirectWithFlash('Artist deactivated successfully.', 'success', '/cms/artists');
        });
    }

    public function activate(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_artist_delete', '/cms/artists');
            $this->artistsService->reactivateArtist($id);
            $this->redirectWithFlash('Artist activated successfully.', 'success', '/cms/artists');
        });
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
        $this->redirectWithFlash('Artist created successfully.', 'success', $this->readSafeReturnTo('/cms/artists'));
    }

    /** Loads the artist by ID, renders 404 if missing, otherwise renders the edit form. */
    private function renderArtistEditPage(int $id): void
    {
        $artist = $this->artistsService->findById($id);
        if ($artist === null) {
            $this->renderNotFoundPage();
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
        $this->redirectWithFlash('Artist updated successfully.', 'success', $this->readSafeReturnTo('/cms/artists'));
    }

    // Rich-text fields (bioHtml, overviewLead, etc.) are read directly from $_POST
    // because readStringPostParam strips tags and would destroy TinyMCE content.
    private function extractFormData(): ArtistUpsertData
    {
        return CmsArtistsMapper::fromFormInput([
            // Identity and card fields
            'name'                   => $this->readStringPostParam('name') ?? '',
            'style'                  => $this->readStringPostParam('style') ?? '',
            'cardDescription'        => $_POST['cardDescription'] ?? '',
            'cardSortOrder'          => $this->readOptionalIntPostParam('cardSortOrder') ?? 0,
            'showOnJazzOverview'     => $this->readBoolPostParam('showOnJazzOverview'),

            // Hero section
            'heroSubtitle'           => $this->readStringPostParam('heroSubtitle') ?? '',
            'heroImagePath'          => $this->readStringPostParam('heroImagePath') ?? '',

            // Biography section — rich-text fields read directly from $_POST (TinyMCE HTML)
            'originText'             => $this->readStringPostParam('originText') ?? '',
            'formedText'             => $this->readStringPostParam('formedText') ?? '',
            'bioHtml'                => $_POST['bioHtml'] ?? '',

            // Overview section
            'overviewLead'           => $_POST['overviewLead'] ?? '',
            'overviewBodySecondary'  => $_POST['overviewBodySecondary'] ?? '',

            // Gallery and media section headings
            'lineupHeading'          => $this->readStringPostParam('lineupHeading') ?? '',
            'highlightsHeading'      => $this->readStringPostParam('highlightsHeading') ?? '',
            'photoGalleryHeading'    => $this->readStringPostParam('photoGalleryHeading') ?? '',
            'photoGalleryDescription' => $_POST['photoGalleryDescription'] ?? '',
            'albumsHeading'          => $this->readStringPostParam('albumsHeading') ?? '',
            'albumsDescription'      => $_POST['albumsDescription'] ?? '',

            // Listen section
            'listenHeading'          => $this->readStringPostParam('listenHeading') ?? '',
            'listenSubheading'       => $this->readStringPostParam('listenSubheading') ?? '',
            'listenDescription'      => $_POST['listenDescription'] ?? '',

            // CTA and performances sections
            'liveCtaHeading'         => $this->readStringPostParam('liveCtaHeading') ?? '',
            'liveCtaDescription'     => $_POST['liveCtaDescription'] ?? '',
            'performancesHeading'    => $this->readStringPostParam('performancesHeading') ?? '',
            'performancesDescription' => $_POST['performancesDescription'] ?? '',

            // Media and status
            'imageAssetId'           => $this->readOptionalIntPostParam('imageAssetId'),
            'isActive'               => $this->readBoolPostParam('isActive'),
        ]);
    }

    /** @param array<string, string> $errors */
    private function buildFormViewModel(?int $artistId, ArtistUpsertData $data, array $errors): \App\ViewModels\Cms\CmsArtistFormViewModel
    {
        // null artistId = create mode; non-null = edit mode — drives scope, action, and title
        $scope  = $artistId === null ? 'cms_artist_create' : 'cms_artist_edit_' . $artistId;
        $action = $artistId === null ? '/cms/artists' : '/cms/artists/' . $artistId . '/edit';
        $title  = $artistId === null ? 'Create Artist' : 'Edit Artist';
        $returnTo = $this->readSafeReturnTo('');
        $backUrl = $returnTo !== '' ? $returnTo : '/cms/artists';
        return CmsArtistsMapper::toFormViewModel($artistId, $data, $this->sessionService->getCsrfToken($scope), $action, $returnTo, $backUrl, $title, $errors);
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

    // When opened via Jazz Overview (?showOnJazzOverview=true), pre-checks the checkbox
    // and pre-fills sort order so the new artist lands in the right position.
    private function buildCreateDefaults(): ArtistUpsertData
    {
        $data = CmsArtistsMapper::emptyData();

        if ($this->readBoolQueryFlag('showOnJazzOverview')) {
            return $data->withJazzOverview($this->resolveInitialJazzCardSortOrder());
        }

        return $data;
    }

    private function resolveInitialJazzCardSortOrder(): int
    {
        $sortOrderParam = $this->readStringQueryParam('cardSortOrder', 32);

        if ($sortOrderParam === 'next') {
            return $this->artistsService->getNextJazzOverviewSortOrder();
        }

        if ($sortOrderParam !== null && ctype_digit($sortOrderParam)) {
            return (int) $sortOrderParam;
        }

        return 0;
    }

    private function readBoolQueryFlag(string $key): bool
    {
        $value = strtolower($this->readStringQueryParam($key, 16) ?? '');
        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function readSafeReturnTo(string $fallback): string
    {
        $candidate = $this->readStringPostParam('returnTo', 2048) ?? $this->readStringQueryParam('returnTo', 2048);
        if ($candidate === null || $candidate === '') {
            return $fallback;
        }

        if (!str_starts_with($candidate, '/') || str_starts_with($candidate, '//')) {
            return $fallback;
        }

        return $candidate;
    }

}

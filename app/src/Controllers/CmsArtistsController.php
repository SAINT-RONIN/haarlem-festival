<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Cms\ArtistUpsertData;
use App\DTOs\Cms\JazzLineupCardUpsertData;
use App\Mappers\CmsArtistsMapper;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsJazzLineupCardFormViewModel;

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
     */
    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'artists';
            $search      = $this->readStringQueryParam('search');
            $viewModel   = $this->buildArtistsListViewModel($search);
            require __DIR__ . '/../Views/pages/cms/artists.php';
        });
    }

    /**
     * Renders the blank artist creation form.
     * GET /cms/artists/create
     */
    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'artists';
            $viewModel   = $this->buildFormViewModel(null, $this->buildCreateDefaults(), []);
            require __DIR__ . '/../Views/pages/cms/artist-create.php';
        });
    }

    /**
     * Validates and persists a new artist from the creation form.
     * POST /cms/artists
     */
    public function store(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->processArtistStore();
        });
    }

    /**
     * Renders the edit form for an existing artist, pre-filled with current data.
     * GET /cms/artists/{id}/edit
     */
    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->renderArtistEditPage($id);
        });
    }

    /**
     * Validates and applies updates to an existing artist.
     * POST /cms/artists/{id}/edit
     */
    public function update(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->processArtistUpdate($id);
        });
    }

    /**
     * Renders the lightweight Jazz lineup card creation form.
     * GET /cms/jazz-lineup/cards/create
     */
    public function createJazzOverviewCard(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'pages';
            $viewModel = $this->buildJazzLineupCardFormViewModel(
                null,
                new JazzLineupCardUpsertData(
                    name: '',
                    style: '',
                    cardDescription: '',
                    imageAssetId: null,
                    cardSortOrder: $this->artistsService->getNextJazzOverviewSortOrder(),
                    isActive: true,
                ),
                [],
            );
            require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
        });
    }

    /**
     * Persists a new lightweight Jazz lineup card.
     * POST /cms/jazz-lineup/cards
     */
    public function storeJazzOverviewCard(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->validateCsrf('cms_jazz_lineup_card_create', $this->readSafeReturnTo('/cms/pages'));
            $data = $this->extractJazzLineupCardFormData();
            $errors = $this->artistsService->validateJazzOverviewCard($data);
            if ($errors !== []) {
                $this->renderJazzLineupCardCreateForm($data, $errors);
                return;
            }

            $this->artistsService->createJazzOverviewCard($data);
            $this->redirectWithFlash('Jazz lineup card created successfully.', 'success', $this->readSafeReturnTo('/cms/pages'));
        });
    }

    /**
     * Renders the lightweight Jazz lineup card edit form.
     * GET /cms/jazz-lineup/cards/{id}/edit
     */
    public function editJazzOverviewCard(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $artist = $this->artistsService->findById($id);
            if ($artist === null) {
                $this->renderNotFoundPage();
                return;
            }

            $currentView = 'pages';
            $viewModel = $this->buildJazzLineupCardFormViewModel(
                $id,
                new JazzLineupCardUpsertData(
                    name: $artist->name,
                    style: $artist->style,
                    cardDescription: $artist->cardDescription,
                    imageAssetId: $artist->imageAssetId,
                    cardSortOrder: $artist->cardSortOrder,
                    isActive: $artist->isActive,
                ),
                [],
            );
            require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
        });
    }

    /**
     * Updates a lightweight Jazz lineup card.
     * POST /cms/jazz-lineup/cards/{id}/edit
     */
    public function updateJazzOverviewCard(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_jazz_lineup_card_edit_' . $id, $this->readSafeReturnTo('/cms/pages'));
            $data = $this->extractJazzLineupCardFormData();
            $errors = $this->artistsService->validateJazzOverviewCard($data);
            if ($errors !== []) {
                $this->renderJazzLineupCardEditForm($id, $data, $errors);
                return;
            }

            $this->artistsService->updateJazzOverviewCard($id, $data);
            $this->redirectWithFlash('Jazz lineup card updated successfully.', 'success', $this->readSafeReturnTo('/cms/pages'));
        });
    }

    /**
     * Adds an existing artist profile to the Jazz overview lineup.
     * POST /cms/artists/{id}/jazz-overview/add
     */
    public function addToJazzOverview(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $returnTo = $this->readSafeReturnTo('/cms/artists');
            $this->validateCsrf('cms_artist_jazz_overview_add', $returnTo);
            $this->artistsService->setJazzOverviewVisibility($id, true);
            $this->redirectWithFlash('Artist added to the Jazz lineup section.', 'success', $returnTo);
        });
    }

    /**
     * Removes an artist profile from the Jazz overview lineup.
     * POST /cms/artists/{id}/jazz-overview/remove
     */
    public function removeFromJazzOverview(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $returnTo = $this->readSafeReturnTo('/cms/artists');
            $this->validateCsrf('cms_artist_jazz_overview_remove', $returnTo);
            $this->artistsService->setJazzOverviewVisibility($id, false);
            $this->redirectWithFlash('Artist removed from the Jazz lineup section.', 'success', $returnTo);
        });
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
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_artist_delete', '/cms/artists');
            $this->artistsService->deleteArtist($id);
            $this->redirectWithFlash('Artist deactivated successfully.', 'success', '/cms/artists');
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

    /**
     * Reads and maps all artist form fields from the current POST request.
     *
     * Rich-text fields (bioHtml, overviewLead, etc.) are read directly from $_POST because
     * they contain TinyMCE HTML and must not be filtered by readStringPostParam, which strips
     * tags. All other fields use the controller helper that trims and length-limits the value.
     *
     * @return ArtistUpsertData Typed data object ready for validation by CmsArtistsService.
     */
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

    /**
     * Builds the default form data for the "Create Artist" page.
     *
     * When the page is opened via the Jazz Overview card list (query flag ?showOnJazzOverview=true),
     * the Jazz Overview checkbox is pre-checked and the sort order is pre-filled so the new artist
     * lands in the right position without manual adjustment.
     *
     * @return ArtistUpsertData Blank defaults, optionally pre-configured for the Jazz Overview list.
     */
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
            return (int)$sortOrderParam;
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

    private function extractJazzLineupCardFormData(): JazzLineupCardUpsertData
    {
        return new JazzLineupCardUpsertData(
            name: $this->readStringPostParam('name') ?? '',
            style: $this->readStringPostParam('style') ?? '',
            cardDescription: trim((string)($_POST['cardDescription'] ?? '')),
            imageAssetId: $this->readOptionalIntPostParam('imageAssetId'),
            cardSortOrder: $this->readOptionalIntPostParam('cardSortOrder') ?? 0,
            isActive: $this->readBoolPostParam('isActive'),
        );
    }

    /**
     * @param array<string, string> $errors
     */
    private function buildJazzLineupCardFormViewModel(
        ?int $artistId,
        JazzLineupCardUpsertData $data,
        array $errors,
    ): CmsJazzLineupCardFormViewModel {
        $scope = $artistId === null ? 'cms_jazz_lineup_card_create' : 'cms_jazz_lineup_card_edit_' . $artistId;
        $action = $artistId === null
            ? '/cms/jazz-lineup/cards'
            : '/cms/jazz-lineup/cards/' . $artistId . '/edit';
        $title = $artistId === null ? 'Create Jazz Lineup Card' : 'Edit Jazz Lineup Card';
        $returnTo = $this->readSafeReturnTo('/cms/pages');
        $backUrl = $returnTo !== '' ? $returnTo : '/cms/pages';

        return new CmsJazzLineupCardFormViewModel(
            artistId: $artistId,
            name: $data->name,
            style: $data->style,
            cardDescription: $data->cardDescription,
            imageAssetId: $data->imageAssetId,
            cardSortOrder: $data->cardSortOrder,
            isActive: $data->isActive,
            csrfToken: $this->sessionService->getCsrfToken($scope),
            formAction: $action,
            pageTitle: $title,
            returnTo: $returnTo,
            backUrl: $backUrl,
            errors: $errors,
        );
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderJazzLineupCardCreateForm(JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel = $this->buildJazzLineupCardFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }

    /**
     * @param array<string, string> $errors
     */
    private function renderJazzLineupCardEditForm(int $id, JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel = $this->buildJazzLineupCardFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }
}

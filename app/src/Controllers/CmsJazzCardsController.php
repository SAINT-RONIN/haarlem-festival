<?php

declare(strict_types=1);

namespace App\Controllers;

use App\DTOs\Cms\JazzLineupCardUpsertData;
use App\Services\Interfaces\ICmsArtistsService;
use App\Services\Interfaces\IMediaAssetService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Cms\CmsArtistOptionViewModel;
use App\ViewModels\Cms\CmsJazzLineupCardFormViewModel;

/**
 * CMS controller for managing lightweight Jazz lineup cards.
 *
 * Jazz lineup cards are stripped-down artist entries that appear on the Jazz
 * overview page. They carry only name, style, card description, image, sort
 * order, and active flag — no full artist biography or social links.
 *
 * Full artist profiles (with biography, gallery, etc.) are managed by
 * CmsArtistsController. An artist can also be shown on the Jazz overview
 * via the addToJazzOverview / removeFromJazzOverview actions on that controller.
 *
 * All mutations delegate to ICmsArtistsService for validation and persistence;
 * this controller owns only HTTP flow (auth gating, CSRF checks, form
 * re-rendering on validation failure, and flash-message redirects via PRG).
 */
class CmsJazzCardsController extends CmsBaseController
{
    /**
     * @param ICmsArtistsService $artistsService Shared service for both artist and Jazz card operations.
     * @param ISessionService    $sessionService  Session, CSRF, and flash-message support.
     */
    public function __construct(
        private readonly ICmsArtistsService $artistsService,
        private readonly IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    /**
     * Renders the blank Jazz lineup card creation form.
     * GET /cms/jazz-lineup/cards/create
     *
     * Pre-fills the sort order with the next available position so the new card
     * lands at the end of the list without manual adjustment.
     */
    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'pages';
            $viewModel   = $this->buildFormViewModel(
                null,
                new JazzLineupCardUpsertData(
                    name:          '',
                    style:         '',
                    cardDescription: '',
                    imageAssetId:  null,
                    cardSortOrder: $this->artistsService->getNextJazzOverviewSortOrder(),
                    isActive:      true,
                ),
                [],
            );
            require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
        });
    }

    /**
     * Validates and persists a new Jazz lineup card.
     * POST /cms/jazz-lineup/cards
     *
     * On validation failure the form is re-rendered with errors. On success the
     * admin is redirected back to the page they came from (returnTo) or /cms/pages.
     */
    public function store(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->validateCsrf('cms_jazz_lineup_card_create', $this->readSafeReturnTo('/cms/pages'));
            $data   = $this->extractFormData();
            $errors = $this->artistsService->validateJazzOverviewCard($data);

            if ($errors !== []) {
                $this->renderCreateForm($data, $errors);
                return;
            }

            $this->artistsService->createJazzOverviewCard($data);
            $this->redirectWithFlash('Jazz lineup card created successfully.', 'success', $this->readSafeReturnTo('/cms/pages'));
        });
    }

    /**
     * Renders the Jazz lineup card edit form pre-filled with the card's current data.
     * GET /cms/jazz-lineup/cards/{id}/edit
     *
     * Returns a 404 page when no card exists for the given ID.
     *
     * @param int $id The artist/card record ID.
     */
    public function edit(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $artist = $this->artistsService->findById($id);

            if ($artist === null) {
                $this->renderNotFoundPage();
                return;
            }

            $currentView = 'pages';
            $viewModel   = $this->buildFormViewModel(
                $id,
                new JazzLineupCardUpsertData(
                    name:            $artist->name,
                    style:           $artist->style,
                    cardDescription: $artist->cardDescription,
                    imageAssetId:    $artist->imageAssetId,
                    cardSortOrder:   $artist->cardSortOrder,
                    isActive:        $artist->isActive,
                ),
                [],
            );
            require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
        });
    }

    /**
     * Validates and applies updates to an existing Jazz lineup card.
     * POST /cms/jazz-lineup/cards/{id}/edit
     *
     * On validation failure the form is re-rendered with errors. On success the
     * admin is redirected back to the returnTo URL or /cms/pages.
     *
     * @param int $id The artist/card record ID.
     */
    public function update(int $id): void
    {
        $this->handleCmsPageRequest(function () use ($id): void {
            $this->validateCsrf('cms_jazz_lineup_card_edit_' . $id, $this->readSafeReturnTo('/cms/pages'));
            $data   = $this->extractFormData();
            $errors = $this->artistsService->validateJazzOverviewCard($data);

            if ($errors !== []) {
                $this->renderEditForm($id, $data, $errors);
                return;
            }

            $this->artistsService->updateJazzOverviewCard($id, $data);
            $this->redirectWithFlash('Jazz lineup card updated successfully.', 'success', $this->readSafeReturnTo('/cms/pages'));
        });
    }

    /**
     * Reads Jazz lineup card fields from the current POST request.
     *
     * cardDescription is read directly from $_POST because it may contain
     * rich text entered via the CMS textarea — the readStringPostParam helper
     * strips tags, which would destroy formatted content.
     *
     * @return JazzLineupCardUpsertData Typed data ready for validation by ICmsArtistsService.
     */
    private function extractFormData(): JazzLineupCardUpsertData
    {
        return new JazzLineupCardUpsertData(
            name:            $this->readStringPostParam('name') ?? '',
            style:           $this->readStringPostParam('style') ?? '',
            cardDescription: trim((string) ($_POST['cardDescription'] ?? '')),
            imageAssetId:    $this->readOptionalIntPostParam('imageAssetId'),
            cardSortOrder:   $this->readOptionalIntPostParam('cardSortOrder') ?? 0,
            isActive:        $this->readBoolPostParam('isActive'),
        );
    }

    /**
     * Builds the form ViewModel for both create and edit modes.
     *
     * The CSRF scope, form action URL, and page title all differ between create
     * and edit — passing null for $cardId selects create mode, a non-null ID
     * selects edit mode.
     *
     * @param int|null                  $cardId The card being edited, or null for creation.
     * @param JazzLineupCardUpsertData  $data   Current field values (blank for create, loaded for edit).
     * @param array<string, string>     $errors Validation errors to display inline on the form.
     * @return CmsJazzLineupCardFormViewModel
     */
    private function buildFormViewModel(?int $cardId, JazzLineupCardUpsertData $data, array $errors): CmsJazzLineupCardFormViewModel
    {
        // null cardId = create mode; non-null = edit mode — drives CSRF scope, action URL, and title
        $scope    = $cardId === null ? 'cms_jazz_lineup_card_create' : 'cms_jazz_lineup_card_edit_' . $cardId;
        $action   = $cardId === null ? '/cms/jazz-lineup/cards' : '/cms/jazz-lineup/cards/' . $cardId . '/edit';
        $title    = $cardId === null ? 'Create Jazz Lineup Card' : 'Edit Jazz Lineup Card';
        $returnTo = $this->readSafeReturnTo('/cms/pages');
        $backUrl  = $returnTo !== '' ? $returnTo : '/cms/pages';

        return new CmsJazzLineupCardFormViewModel(
            artistId:        $cardId,
            name:            $data->name,
            style:           $data->style,
            cardDescription: $data->cardDescription,
            imageAssetId:    $data->imageAssetId,
            imageUrl:        $this->resolveImageUrl($data->imageAssetId),
            cardSortOrder:   $data->cardSortOrder,
            isActive:        $data->isActive,
            artists:         $this->buildArtistOptions(),
            csrfToken:       $this->sessionService->getCsrfToken($scope),
            formAction:      $action,
            pageTitle:       $title,
            returnTo:        $returnTo,
            backUrl:         $backUrl,
            errors:          $errors,
        );
    }

    /**
     * Re-renders the creation form with validation errors.
     *
     * @param JazzLineupCardUpsertData $data   The submitted values to repopulate the form.
     * @param array<string, string>    $errors Validation errors keyed by field name.
     */
    private function renderCreateForm(JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel   = $this->buildFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }

    /**
     * Re-renders the edit form with validation errors.
     *
     * @param int                      $id     The card being edited.
     * @param JazzLineupCardUpsertData $data   The submitted values to repopulate the form.
     * @param array<string, string>    $errors Validation errors keyed by field name.
     */
    private function renderEditForm(int $id, JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel   = $this->buildFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }

    /**
     * Builds a list of all active artists for the dropdown selector.
     *
     * @return CmsArtistOptionViewModel[]
     */
    private function buildArtistOptions(): array
    {
        $artists = $this->artistsService->getArtists(null);
        return array_values(array_map(
            function (\App\Models\Artist $artist): CmsArtistOptionViewModel {
                return new CmsArtistOptionViewModel(
                    artistId:    $artist->artistId,
                    name:        $artist->name,
                    style:       $artist->style,
                    description: $artist->cardDescription,
                    imageAssetId: $artist->imageAssetId,
                    imageUrl:     $artist->imagePath ?? '',
                );
            },
            $artists,
        ));
    }

    /**
     * Resolves the public URL for a media asset by its ID.
     *
     * @param int|null $imageAssetId
     * @return string Empty string when no asset is linked or not found.
     */
    private function resolveImageUrl(?int $imageAssetId): string
    {
        if ($imageAssetId === null || $imageAssetId <= 0) {
            return '';
        }

        $asset = $this->mediaAssetService->getAssetById($imageAssetId);
        if ($asset === null) {
            return '';
        }

        return $asset->filePath;
    }

    /**
     * Reads and validates the returnTo redirect target from POST or GET.
     *
     * Only accepts paths that start with a single slash — rejects empty strings,
     * protocol-relative URLs (//), and any non-path values to prevent open redirects.
     *
     * @param string $fallback Returned when no valid returnTo value is present.
     * @return string A safe relative URL path, or $fallback.
     */
    private function readSafeReturnTo(string $fallback): string
    {
        $candidate = $this->readStringPostParam('returnTo', 2048) ?? $this->readStringQueryParam('returnTo', 2048);

        if ($candidate === null || $candidate === '') {
            return $fallback;
        }

        // Reject protocol-relative URLs (//example.com) and non-path values
        if (!str_starts_with($candidate, '/') || str_starts_with($candidate, '//')) {
            return $fallback;
        }

        return $candidate;
    }
}

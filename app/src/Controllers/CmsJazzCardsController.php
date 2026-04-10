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
 * CMS controller for managing lightweight Jazz lineup cards (name, style, image, sort order).
 *
 * Full artist profiles are managed by CmsArtistsController.
 */
class CmsJazzCardsController extends CmsBaseController
{
    public function __construct(
        private readonly ICmsArtistsService $artistsService,
        private readonly IMediaAssetService $mediaAssetService,
        ISessionService $sessionService,
    ) {
        parent::__construct($sessionService);
    }

    // Pre-fills sort order with the next available position.
    public function create(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $currentView = 'pages';
            $viewModel   = $this->buildFormViewModel(
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

    // cardDescription is read directly from $_POST to preserve rich text (readStringPostParam strips tags).
    private function extractFormData(): JazzLineupCardUpsertData
    {
        return new JazzLineupCardUpsertData(
            name: $this->readStringPostParam('name') ?? '',
            style: $this->readStringPostParam('style') ?? '',
            cardDescription: trim((string) ($_POST['cardDescription'] ?? '')),
            imageAssetId: $this->readOptionalIntPostParam('imageAssetId'),
            cardSortOrder: $this->readOptionalIntPostParam('cardSortOrder') ?? 0,
            isActive: $this->readBoolPostParam('isActive'),
        );
    }

    /** @param array<string, string> $errors */
    private function buildFormViewModel(?int $cardId, JazzLineupCardUpsertData $data, array $errors): CmsJazzLineupCardFormViewModel
    {
        // null cardId = create mode; non-null = edit mode — drives CSRF scope, action URL, and title
        $scope    = $cardId === null ? 'cms_jazz_lineup_card_create' : 'cms_jazz_lineup_card_edit_' . $cardId;
        $action   = $cardId === null ? '/cms/jazz-lineup/cards' : '/cms/jazz-lineup/cards/' . $cardId . '/edit';
        $title    = $cardId === null ? 'Create Jazz Lineup Card' : 'Edit Jazz Lineup Card';
        $returnTo = $this->readSafeReturnTo('/cms/pages');
        $backUrl  = $returnTo !== '' ? $returnTo : '/cms/pages';

        return new CmsJazzLineupCardFormViewModel(
            artistId: $cardId,
            name: $data->name,
            style: $data->style,
            cardDescription: $data->cardDescription,
            imageAssetId: $data->imageAssetId,
            imageUrl: $this->resolveImageUrl($data->imageAssetId),
            cardSortOrder: $data->cardSortOrder,
            isActive: $data->isActive,
            artists: $this->buildArtistOptions(),
            csrfToken: $this->sessionService->getCsrfToken($scope),
            formAction: $action,
            pageTitle: $title,
            returnTo: $returnTo,
            backUrl: $backUrl,
            errors: $errors,
        );
    }

    /** @param array<string, string> $errors */
    private function renderCreateForm(JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel   = $this->buildFormViewModel(null, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }

    /** @param array<string, string> $errors */
    private function renderEditForm(int $id, JazzLineupCardUpsertData $data, array $errors): void
    {
        $currentView = 'pages';
        $viewModel   = $this->buildFormViewModel($id, $data, $errors);
        require __DIR__ . '/../Views/pages/cms/jazz-lineup-card-form.php';
    }

    /** @return CmsArtistOptionViewModel[] */
    private function buildArtistOptions(): array
    {
        $artists = $this->artistsService->getArtists(null);
        return array_values(array_map(
            function (\App\Models\Artist $artist): CmsArtistOptionViewModel {
                return new CmsArtistOptionViewModel(
                    artistId: $artist->artistId,
                    name: $artist->name,
                    style: $artist->style,
                    description: $artist->cardDescription,
                    imageAssetId: $artist->imageAssetId,
                    imageUrl: $artist->imagePath ?? '',
                );
            },
            $artists,
        ));
    }

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

    // Only accepts paths starting with a single slash to prevent open redirects.
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

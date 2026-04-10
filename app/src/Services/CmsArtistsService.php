<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
use App\DTOs\Cms\JazzLineupCardUpsertData;
use App\Exceptions\CmsOperationException;
use App\Helpers\FieldValidator;
use App\Helpers\TextHelper;
use App\Repositories\Interfaces\IArtistRepository;
use App\Services\Interfaces\ICmsArtistsService;

class CmsArtistsService implements ICmsArtistsService
{
    public function __construct(
        private readonly IArtistRepository $artistRepository,
    ) {}

    /** @return Artist[] */
    public function getArtists(?string $search): array
    {
        return $this->artistRepository->findAll($search);
    }

    public function findById(int $id): ?Artist
    {
        return $this->artistRepository->findById($id);
    }

    /** @return array<string, string> */
    public function validateForCreate(ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** @return array<string, string> */
    public function validateForUpdate(int $_id, ArtistUpsertData $data): array
    {
        // $_id is not needed — no uniqueness constraint to exclude on artists
        return $this->validate($data);
    }

    /** @throws CmsOperationException */
    public function createArtist(ArtistUpsertData $data): int
    {
        try {
            return $this->artistRepository->create($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create artist.', 0, $error);
        }
    }

    /** @return array<string, string> */
    public function validateJazzOverviewCard(JazzLineupCardUpsertData $data): array
    {
        return $this->validateJazzCard($data);
    }

    /** @throws CmsOperationException */
    public function createJazzOverviewCard(JazzLineupCardUpsertData $data): int
    {
        try {
            return $this->artistRepository->createJazzOverviewCard($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create Jazz lineup card.', 0, $error);
        }
    }

    /** @throws CmsOperationException */
    public function updateArtist(int $id, ArtistUpsertData $data): void
    {
        try {
            $this->artistRepository->update($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update artist.', 0, $error);
        }
    }

    /** @throws CmsOperationException */
    public function updateJazzOverviewCard(int $id, JazzLineupCardUpsertData $data): void
    {
        try {
            $this->artistRepository->updateJazzOverviewCard($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup card.', 0, $error);
        }
    }

    // Using next available value as default means a new card always appears at the end of the grid.
    public function getNextJazzOverviewSortOrder(): int
    {
        return $this->artistRepository->getNextJazzOverviewSortOrder();
    }

    /** @throws CmsOperationException */
    public function setJazzOverviewVisibility(int $id, bool $visible): void
    {
        try {
            $this->artistRepository->setJazzOverviewVisibility($id, $visible);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup visibility.', 0, $error);
        }
    }

    // Repository must also remove related Jazz lineup cards (FK constraint).
    /** @throws CmsOperationException */
    public function deleteArtist(int $id): void
    {
        try {
            $this->artistRepository->delete($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete artist.', 0, $error);
        }
    }

    /** @throws CmsOperationException */
    public function reactivateArtist(int $id): void
    {
        try {
            $this->artistRepository->reactivate($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to reactivate artist.', 0, $error);
        }
    }

    /** @return array<string, string> */
    private function validate(ArtistUpsertData $data): array
    {
        $errors = [];
        FieldValidator::requireNonEmpty('name', $data->name, 'Name', $errors);
        FieldValidator::requireNonEmpty('style', $data->style, 'Style', $errors);
        FieldValidator::requireNonEmpty('cardDescription', $data->cardDescription, 'Card description', $errors);
        FieldValidator::requireNonEmpty('bioHtml', TextHelper::stripHtmlToText($data->bioHtml), 'Bio', $errors);
        if ($data->cardSortOrder < 0) {
            $errors['cardSortOrder'] = 'Card sort order must be 0 or greater.';
        }
        return $errors;
    }

    // Jazz cards don't require a bio, unlike full artist profiles.
    /** @return array<string, string> */
    private function validateJazzCard(JazzLineupCardUpsertData $data): array
    {
        $errors = [];
        FieldValidator::requireNonEmpty('name', $data->name, 'Name', $errors);
        FieldValidator::requireNonEmpty('style', $data->style, 'Style', $errors);
        FieldValidator::requireNonEmpty('cardDescription', $data->cardDescription, 'Card description', $errors);
        if ($data->cardSortOrder < 0) {
            $errors['cardSortOrder'] = 'Card sort order must be 0 or greater.';
        }

        return $errors;
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
use App\DTOs\Cms\JazzLineupCardUpsertData;
use App\Exceptions\CmsOperationException;
use App\Helpers\FieldValidator;
use App\Repositories\Interfaces\IArtistRepository;
use App\Services\Interfaces\ICmsArtistsService;

/**
 * CMS-side artist management: listing, creating, updating, and deleting artist profiles.
 *
 * Validates that required fields (name, style, bio) are present before persisting changes.
 */
class CmsArtistsService implements ICmsArtistsService
{
    public function __construct(
        private readonly IArtistRepository $artistRepository,
    ) {}

    /**
     * Returns all artists, optionally filtered by a search term matching name or style.
     *
     * @return Artist[]
     */
    public function getArtists(?string $search): array
    {
        return $this->artistRepository->findAll($search);
    }

    /** Loads one artist for the CMS edit page. */
    public function findById(int $id): ?Artist
    {
        return $this->artistRepository->findById($id);
    }

    /**
     * Validates artist data before creation. Returns field-level errors if any required fields are empty.
     *
     * @return array<string, string> Field name => error message (empty if valid)
     */
    public function validateForCreate(ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    /**
     * Validates artist data before update. Same rules as creation.
     *
     * @return array<string, string> Field name => error message (empty if valid)
     */
    public function validateForUpdate(int $id, ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** @throws CmsOperationException When the database write fails */
    public function createArtist(ArtistUpsertData $data): int
    {
        try {
            return $this->artistRepository->create($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create artist.', 0, $error);
        }
    }

    public function validateJazzOverviewCard(JazzLineupCardUpsertData $data): array
    {
        return $this->validateJazzCard($data);
    }

    /** @throws CmsOperationException When the database write fails */
    public function createJazzOverviewCard(JazzLineupCardUpsertData $data): int
    {
        try {
            return $this->artistRepository->createJazzOverviewCard($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create Jazz lineup card.', 0, $error);
        }
    }

    /** @throws CmsOperationException When the database write fails */
    public function updateArtist(int $id, ArtistUpsertData $data): void
    {
        try {
            $this->artistRepository->update($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update artist.', 0, $error);
        }
    }

    /** @throws CmsOperationException When the database write fails */
    public function updateJazzOverviewCard(int $id, JazzLineupCardUpsertData $data): void
    {
        try {
            $this->artistRepository->updateJazzOverviewCard($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup card.', 0, $error);
        }
    }

    public function getNextJazzOverviewSortOrder(): int
    {
        return $this->artistRepository->getNextJazzOverviewSortOrder();
    }

    /** @throws CmsOperationException When the database write fails */
    public function setJazzOverviewVisibility(int $id, bool $visible): void
    {
        try {
            $this->artistRepository->setJazzOverviewVisibility($id, $visible);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup visibility.', 0, $error);
        }
    }

    /** @throws CmsOperationException When the database write fails */
    public function deleteArtist(int $id): void
    {
        try {
            $this->artistRepository->delete($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete artist.', 0, $error);
        }
    }

    /** @return array<string, string> */
    private function validate(ArtistUpsertData $data): array
    {
        $errors = [];
        FieldValidator::requireNonEmpty('name', $data->name, 'Name', $errors);
        FieldValidator::requireNonEmpty('style', $data->style, 'Style', $errors);
        FieldValidator::requireNonEmpty('cardDescription', $data->cardDescription, 'Card description', $errors);
        FieldValidator::requireNonEmpty('bioHtml', $data->bioHtml, 'Bio', $errors);
        if ($data->cardSortOrder < 0) {
            $errors['cardSortOrder'] = 'Card sort order must be 0 or greater.';
        }
        return $errors;
    }

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

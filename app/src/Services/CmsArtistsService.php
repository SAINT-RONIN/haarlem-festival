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
     * Passing null returns every artist. Passing a search string narrows the list
     * to artists whose name contains that string (case-insensitive).
     *
     * @return Artist[]
     */
    public function getArtists(?string $search): array
    {
        return $this->artistRepository->findAll($search);
    }

    /**
     * Loads a single artist by id, used by the CMS edit page before rendering the form.
     *
     * Returns null when no artist exists with that id — the controller uses null to send a 404.
     */
    public function findById(int $id): ?Artist
    {
        return $this->artistRepository->findById($id);
    }

    /**
     * Validates an artist record before creation.
     *
     * Delegates entirely to the shared validate() helper — there are no extra rules
     * specific to creation (no uniqueness constraints on artist fields).
     *
     * @return array<string, string> Field name => error message, empty if everything is valid
     */
    public function validateForCreate(ArtistUpsertData $data): array
    {
        return $this->validate($data);
    }

    /**
     * Validates an artist record before update. Same rules as creation.
     *
     * $id is accepted for symmetry with other services but is not used in validation
     * because artist fields have no uniqueness constraints tied to a specific artist's identity.
     *
     * @return array<string, string> Field name => error message, empty if everything is valid
     */
    public function validateForUpdate(int $id, ArtistUpsertData $data): array
    {
        // $id is not needed here — no uniqueness constraint to exclude
        return $this->validate($data);
    }

    /**
     * Creates a new artist record and returns the new artist's id.
     *
     * The id is returned so the controller can redirect to the edit page immediately after creation.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function createArtist(ArtistUpsertData $data): int
    {
        try {
            return $this->artistRepository->create($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create artist.', 0, $error);
        }
    }

    /**
     * Validates a Jazz lineup card before creation or update.
     *
     * A Jazz overview card is a trimmed-down entry shown in the lineup grid. It needs
     * fewer fields than a full artist profile (no bio, no full sort-order rules).
     *
     * @return array<string, string> Field name => error message, empty if everything is valid
     */
    public function validateJazzOverviewCard(JazzLineupCardUpsertData $data): array
    {
        return $this->validateJazzCard($data);
    }

    /**
     * Creates a new Jazz lineup card entry and returns its id.
     *
     * A Jazz lineup card is different from a full artist record — it is a card shown
     * in the overview grid on the Jazz page and has its own table row.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function createJazzOverviewCard(JazzLineupCardUpsertData $data): int
    {
        try {
            return $this->artistRepository->createJazzOverviewCard($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create Jazz lineup card.', 0, $error);
        }
    }

    /**
     * Updates an artist record, replacing all fields with the values in $data.
     *
     * All fields are replaced — partial updates are not supported. Pass the full set of
     * current values for any field you do not want to change.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function updateArtist(int $id, ArtistUpsertData $data): void
    {
        try {
            $this->artistRepository->update($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update artist.', 0, $error);
        }
    }

    /**
     * Updates a Jazz lineup card, replacing all its fields with the values in $data.
     *
     * Same full-replacement behaviour as updateArtist.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function updateJazzOverviewCard(int $id, JazzLineupCardUpsertData $data): void
    {
        try {
            $this->artistRepository->updateJazzOverviewCard($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup card.', 0, $error);
        }
    }

    /**
     * Returns the next available sort order value for a new Jazz lineup card.
     *
     * Sort order controls the display position in the lineup grid. Using the next
     * available value as the default means a new card always appears at the end.
     */
    public function getNextJazzOverviewSortOrder(): int
    {
        return $this->artistRepository->getNextJazzOverviewSortOrder();
    }

    /**
     * Shows or hides a Jazz lineup card in the public overview grid.
     *
     * Hiding a card removes it from the public grid without deleting the data.
     * The card can be made visible again at any time.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function setJazzOverviewVisibility(int $id, bool $visible): void
    {
        try {
            $this->artistRepository->setJazzOverviewVisibility($id, $visible);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update Jazz lineup visibility.', 0, $error);
        }
    }

    /**
     * Permanently deletes an artist record.
     *
     * This is a hard delete with no soft-delete fallback. The repository must also remove
     * any related Jazz lineup cards or the delete will fail with a foreign-key constraint.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function deleteArtist(int $id): void
    {
        try {
            $this->artistRepository->delete($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete artist.', 0, $error);
        }
    }

    /**
     * Reactivates a previously deactivated artist.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function reactivateArtist(int $id): void
    {
        try {
            $this->artistRepository->reactivate($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to reactivate artist.', 0, $error);
        }
    }

    /**
     * Shared validation for full artist records, used by both create and update.
     *
     * All four text fields are required via FieldValidator. The sort-order range check
     * is done inline because FieldValidator does not have a numeric-range rule.
     *
     * @return array<string, string>
     */
    private function validate(ArtistUpsertData $data): array
    {
        $errors = [];
        FieldValidator::requireNonEmpty('name', $data->name, 'Name', $errors);
        FieldValidator::requireNonEmpty('style', $data->style, 'Style', $errors);
        FieldValidator::requireNonEmpty('cardDescription', $data->cardDescription, 'Card description', $errors);
        FieldValidator::requireNonEmpty('bioHtml', $data->bioHtml, 'Bio', $errors);
        // FieldValidator has no range rule, so we check the lower bound inline.
        if ($data->cardSortOrder < 0) {
            $errors['cardSortOrder'] = 'Card sort order must be 0 or greater.';
        }
        return $errors;
    }

    /**
     * Validation for Jazz lineup cards, which need fewer fields than a full artist profile.
     *
     * Bio is not required here because the lineup grid only shows the card description.
     * The sort-order range check is the same as for full artist records.
     *
     * @return array<string, string>
     */
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

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
use App\DTOs\Cms\JazzLineupCardUpsertData;
use App\Repositories\Interfaces\IArtistRepository;

/**
 * Provides CRUD operations against the Artist table.
 *
 * Supports optional name-based search for the artist listing and uses
 * soft-delete (IsActive = 0) instead of removing rows.
 */
class ArtistRepository extends BaseRepository implements IArtistRepository
{
    /**
     * Retrieves all artists, optionally filtered by a partial name match.
     *
     * @return Artist[]
     */
    public function findAll(?string $search = null): array
    {
        $sql = 'SELECT a.*, ma.FilePath AS ImagePath FROM Artist a LEFT JOIN MediaAsset ma ON ma.MediaAssetId = a.ImageAssetId';
        $params = [];
        if ($search !== null && $search !== '') {
            $sql .= ' WHERE a.Name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY a.Name ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => Artist::fromRow($row));
    }

    /**
     * Looks up a single artist by primary key, or null if not found.
     */
    public function findById(int $id): ?Artist
    {
        return $this->fetchOne(
            'SELECT a.*, ma.FilePath AS ImagePath
             FROM Artist a
             LEFT JOIN MediaAsset ma ON ma.MediaAssetId = a.ImageAssetId
             WHERE a.ArtistId = :id
             LIMIT 1',
            [':id' => $id],
            fn(array $row) => Artist::fromRow($row),
        );
    }

    /**
     * Inserts a new artist and returns the auto-incremented ID.
     */
    public function create(ArtistUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO Artist (
                Name, Style, CardDescription, HeroSubtitle, HeroImagePath, OriginText, FormedText,
                BioHtml, OverviewLead, OverviewBodySecondary, LineupHeading, HighlightsHeading,
                PhotoGalleryHeading, PhotoGalleryDescription, AlbumsHeading, AlbumsDescription,
                ListenHeading, ListenSubheading, ListenDescription, LiveCtaHeading, LiveCtaDescription,
                PerformancesHeading, PerformancesDescription, CardSortOrder, ShowOnJazzOverview,
                ImageAssetId, IsActive, CreatedAtUtc
             ) VALUES (
                :name, :style, :cardDescription, :heroSubtitle, :heroImagePath, :originText, :formedText,
                :bio, :overviewLead, :overviewBodySecondary, :lineupHeading, :highlightsHeading,
                :photoGalleryHeading, :photoGalleryDescription, :albumsHeading, :albumsDescription,
                :listenHeading, :listenSubheading, :listenDescription, :liveCtaHeading, :liveCtaDescription,
                :performancesHeading, :performancesDescription, :cardSortOrder, :showOnJazzOverview,
                :imageId, :active, NOW()
             )',
            [
                ':name' => $data->name,
                ':style' => $data->style,
                ':cardDescription' => $data->cardDescription,
                ':heroSubtitle' => $data->heroSubtitle,
                ':heroImagePath' => $data->heroImagePath,
                ':originText' => $data->originText,
                ':formedText' => $data->formedText,
                ':bio' => $data->bioHtml,
                ':overviewLead' => $data->overviewLead,
                ':overviewBodySecondary' => $data->overviewBodySecondary,
                ':lineupHeading' => $data->lineupHeading,
                ':highlightsHeading' => $data->highlightsHeading,
                ':photoGalleryHeading' => $data->photoGalleryHeading,
                ':photoGalleryDescription' => $data->photoGalleryDescription,
                ':albumsHeading' => $data->albumsHeading,
                ':albumsDescription' => $data->albumsDescription,
                ':listenHeading' => $data->listenHeading,
                ':listenSubheading' => $data->listenSubheading,
                ':listenDescription' => $data->listenDescription,
                ':liveCtaHeading' => $data->liveCtaHeading,
                ':liveCtaDescription' => $data->liveCtaDescription,
                ':performancesHeading' => $data->performancesHeading,
                ':performancesDescription' => $data->performancesDescription,
                ':cardSortOrder' => $data->cardSortOrder,
                ':showOnJazzOverview' => $data->showOnJazzOverview ? 1 : 0,
                ':imageId' => $data->imageAssetId,
                ':active' => $data->isActive ? 1 : 0,
            ],
        );
    }

    public function createJazzOverviewCard(JazzLineupCardUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO Artist (
                Name, Style, CardDescription, ImageAssetId, CardSortOrder,
                ShowOnJazzOverview, IsActive, CreatedAtUtc
             ) VALUES (
                :name, :style, :cardDescription, :imageId, :cardSortOrder,
                1, :active, NOW()
             )',
            [
                ':name' => $data->name,
                ':style' => $data->style,
                ':cardDescription' => $data->cardDescription,
                ':imageId' => $data->imageAssetId,
                ':cardSortOrder' => $data->cardSortOrder,
                ':active' => $data->isActive ? 1 : 0,
            ],
        );
    }

    /**
     * Overwrites all mutable fields of an existing artist.
     */
    public function update(int $id, ArtistUpsertData $data): void
    {
        $this->execute(
            'UPDATE Artist SET
                Name = :name,
                Style = :style,
                CardDescription = :cardDescription,
                HeroSubtitle = :heroSubtitle,
                HeroImagePath = :heroImagePath,
                OriginText = :originText,
                FormedText = :formedText,
                BioHtml = :bio,
                OverviewLead = :overviewLead,
                OverviewBodySecondary = :overviewBodySecondary,
                LineupHeading = :lineupHeading,
                HighlightsHeading = :highlightsHeading,
                PhotoGalleryHeading = :photoGalleryHeading,
                PhotoGalleryDescription = :photoGalleryDescription,
                AlbumsHeading = :albumsHeading,
                AlbumsDescription = :albumsDescription,
                ListenHeading = :listenHeading,
                ListenSubheading = :listenSubheading,
                ListenDescription = :listenDescription,
                LiveCtaHeading = :liveCtaHeading,
                LiveCtaDescription = :liveCtaDescription,
                PerformancesHeading = :performancesHeading,
                PerformancesDescription = :performancesDescription,
                CardSortOrder = :cardSortOrder,
                ShowOnJazzOverview = :showOnJazzOverview,
                ImageAssetId = :imageId,
                IsActive = :active
             WHERE ArtistId = :id',
            [
                ':id' => $id,
                ':name' => $data->name,
                ':style' => $data->style,
                ':cardDescription' => $data->cardDescription,
                ':heroSubtitle' => $data->heroSubtitle,
                ':heroImagePath' => $data->heroImagePath,
                ':originText' => $data->originText,
                ':formedText' => $data->formedText,
                ':bio' => $data->bioHtml,
                ':overviewLead' => $data->overviewLead,
                ':overviewBodySecondary' => $data->overviewBodySecondary,
                ':lineupHeading' => $data->lineupHeading,
                ':highlightsHeading' => $data->highlightsHeading,
                ':photoGalleryHeading' => $data->photoGalleryHeading,
                ':photoGalleryDescription' => $data->photoGalleryDescription,
                ':albumsHeading' => $data->albumsHeading,
                ':albumsDescription' => $data->albumsDescription,
                ':listenHeading' => $data->listenHeading,
                ':listenSubheading' => $data->listenSubheading,
                ':listenDescription' => $data->listenDescription,
                ':liveCtaHeading' => $data->liveCtaHeading,
                ':liveCtaDescription' => $data->liveCtaDescription,
                ':performancesHeading' => $data->performancesHeading,
                ':performancesDescription' => $data->performancesDescription,
                ':cardSortOrder' => $data->cardSortOrder,
                ':showOnJazzOverview' => $data->showOnJazzOverview ? 1 : 0,
                ':imageId' => $data->imageAssetId,
                ':active' => $data->isActive ? 1 : 0,
            ],
        );
    }

    public function updateJazzOverviewCard(int $id, JazzLineupCardUpsertData $data): void
    {
        $this->execute(
            'UPDATE Artist SET
                Name = :name,
                Style = :style,
                CardDescription = :cardDescription,
                ImageAssetId = :imageId,
                CardSortOrder = :cardSortOrder,
                ShowOnJazzOverview = 1,
                IsActive = :active
             WHERE ArtistId = :id',
            [
                ':id' => $id,
                ':name' => $data->name,
                ':style' => $data->style,
                ':cardDescription' => $data->cardDescription,
                ':imageId' => $data->imageAssetId,
                ':cardSortOrder' => $data->cardSortOrder,
                ':active' => $data->isActive ? 1 : 0,
            ],
        );
    }

    public function getNextJazzOverviewSortOrder(): int
    {
        $statement = $this->execute(
            'SELECT COALESCE(MAX(CardSortOrder), 0) + 1 AS NextSortOrder
             FROM Artist
             WHERE IsActive = 1
               AND ShowOnJazzOverview = 1'
        );

        $value = $statement->fetchColumn();
        return max(1, (int) $value);
    }

    public function setJazzOverviewVisibility(int $id, bool $visible): void
    {
        if (!$visible) {
            $this->execute(
                'UPDATE Artist SET ShowOnJazzOverview = 0 WHERE ArtistId = :id',
                [':id' => $id],
            );

            return;
        }

        $sortOrderStatement = $this->execute(
            'SELECT CardSortOrder FROM Artist WHERE ArtistId = :id LIMIT 1',
            [':id' => $id],
        );
        $currentSortOrder = (int) ($sortOrderStatement->fetchColumn() ?: 0);
        $sortOrder = $currentSortOrder > 0 ? $currentSortOrder : $this->getNextJazzOverviewSortOrder();

        $this->execute(
            'UPDATE Artist
             SET ShowOnJazzOverview = 1,
                 CardSortOrder = CASE WHEN CardSortOrder > 0 THEN CardSortOrder ELSE :sortOrder END
             WHERE ArtistId = :id',
            [
                ':id' => $id,
                ':sortOrder' => $sortOrder,
            ],
        );
    }

    /**
     * Soft-deletes an artist by setting IsActive to 0 (row is preserved for FK integrity).
     */
    public function delete(int $id): void
    {
        $this->execute('UPDATE Artist SET IsActive = 0 WHERE ArtistId = :id', [':id' => $id]);
    }

    /**
     * Reactivates a previously deleted artist by setting IsActive to 1.
     */
    public function reactivate(int $id): void
    {
        $this->execute('UPDATE Artist SET IsActive = 1 WHERE ArtistId = :id', [':id' => $id]);
    }
}

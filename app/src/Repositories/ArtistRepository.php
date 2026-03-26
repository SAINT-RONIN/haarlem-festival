<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Artist;
use App\DTOs\Cms\ArtistUpsertData;
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
        $sql = 'SELECT * FROM Artist';
        $params = [];
        if ($search !== null && $search !== '') {
            $sql .= ' WHERE Name LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY Name ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => Artist::fromRow($row));
    }

    /**
     * Looks up a single artist by primary key, or null if not found.
     */
    public function findById(int $id): ?Artist
    {
        return $this->fetchOne(
            'SELECT * FROM Artist WHERE ArtistId = :id LIMIT 1',
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
            'INSERT INTO Artist (Name, Style, BioHtml, ImageAssetId, IsActive, CreatedAtUtc)
             VALUES (:name, :style, :bio, :imageId, :active, NOW())',
            [
                ':name' => $data->name, ':style' => $data->style, ':bio' => $data->bioHtml,
                ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
            ],
        );
    }

    /**
     * Overwrites all mutable fields of an existing artist.
     */
    public function update(int $id, ArtistUpsertData $data): void
    {
        $this->execute(
            'UPDATE Artist SET Name=:name, Style=:style, BioHtml=:bio,
             ImageAssetId=:imageId, IsActive=:active WHERE ArtistId=:id',
            [
                ':id' => $id, ':name' => $data->name, ':style' => $data->style, ':bio' => $data->bioHtml,
                ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
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
}

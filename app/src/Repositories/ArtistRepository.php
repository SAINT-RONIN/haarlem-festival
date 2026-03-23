<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Artist;
use App\Models\ArtistUpsertData;
use App\Repositories\Interfaces\IArtistRepository;
use PDO;

/**
 * Provides CRUD operations against the Artist table.
 *
 * Supports optional name-based search for the artist listing and uses
 * soft-delete (IsActive = 0) instead of removing rows.
 */
class ArtistRepository implements IArtistRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map([Artist::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Looks up a single artist by primary key, or null if not found.
     */
    public function findById(int $id): ?Artist
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Artist WHERE ArtistId = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? Artist::fromRow($row) : null;
    }

    /**
     * Inserts a new artist and returns the auto-incremented ID.
     */
    public function create(ArtistUpsertData $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Artist (Name, Style, BioHtml, ImageAssetId, IsActive, CreatedAtUtc)
             VALUES (:name, :style, :bio, :imageId, :active, NOW())'
        );
        $stmt->execute([
            ':name' => $data->name, ':style' => $data->style, ':bio' => $data->bioHtml,
            ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Overwrites all mutable fields of an existing artist.
     */
    public function update(int $id, ArtistUpsertData $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE Artist SET Name=:name, Style=:style, BioHtml=:bio,
             ImageAssetId=:imageId, IsActive=:active WHERE ArtistId=:id'
        );
        $stmt->execute([
            ':id' => $id, ':name' => $data->name, ':style' => $data->style, ':bio' => $data->bioHtml,
            ':imageId' => $data->imageAssetId, ':active' => $data->isActive ? 1 : 0,
        ]);
    }

    /**
     * Soft-deletes an artist by setting IsActive to 0 (row is preserved for FK integrity).
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE Artist SET IsActive = 0 WHERE ArtistId = :id');
        $stmt->execute([':id' => $id]);
    }
}

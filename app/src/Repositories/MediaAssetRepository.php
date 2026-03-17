<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;
use PDO;

/**
 * Repository for MediaAsset database operations.
 */
class MediaAssetRepository implements IMediaAssetRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Finds a media asset by ID.
     *
     * @param int $mediaAssetId
     * @return MediaAsset|null
     */
    public function findById(int $mediaAssetId): ?MediaAsset
    {
        $stmt = $this->pdo->prepare('SELECT * FROM MediaAsset WHERE MediaAssetId = ?');
        $stmt->execute([$mediaAssetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? MediaAsset::fromRow($result) : null;
    }

    /**
     * @param int[] $ids
     * @return array<int, MediaAsset> Keyed by MediaAssetId
     */
    public function findByIds(array $ids): array
    {
        $ids = array_filter($ids, fn(int $id) => $id > 0);
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT * FROM MediaAsset WHERE MediaAssetId IN ({$placeholders})");
        $stmt->execute(array_values($ids));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $assets = [];
        foreach ($rows as $row) {
            $asset = MediaAsset::fromRow($row);
            $assets[$asset->mediaAssetId] = $asset;
        }

        return $assets;
    }

    /**
     * Creates a new media asset record.
     *
     * @param array $data Keys: FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText
     * @return int The new MediaAssetId
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText) 
                VALUES (?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['FilePath'],
            $data['OriginalFileName'],
            $data['MimeType'],
            $data['FileSizeBytes'],
            $data['AltText'] ?? ''
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Updates a media asset record.
     */
    public function update(int $mediaAssetId, array $data): bool
    {
        $fields = [];
        $values = [];

        foreach (['FilePath', 'OriginalFileName', 'MimeType', 'FileSizeBytes', 'AltText'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $mediaAssetId;
        $sql = 'UPDATE MediaAsset SET ' . implode(', ', $fields) . ' WHERE MediaAssetId = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Deletes a media asset record.
     */
    public function delete(int $mediaAssetId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM MediaAsset WHERE MediaAssetId = ?');
        return $stmt->execute([$mediaAssetId]);
    }

    /**
     * Links a media asset to a CMS item by updating the CmsItem's MediaAssetId.
     *
     * @param int $mediaAssetId Media asset ID
     * @param int $cmsItemId CMS item ID
     * @return bool Success status
     */
    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE CmsItem SET MediaAssetId = ? WHERE CmsItemId = ?');
        return $stmt->execute([$mediaAssetId, $cmsItemId]);
    }

    /**
     * Returns all media assets ordered by newest first.
     *
     * @return MediaAsset[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM MediaAsset ORDER BY CreatedAtUtc DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([MediaAsset::class, 'fromRow'], $rows);
    }
}

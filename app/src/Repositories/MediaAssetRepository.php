<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;
use PDO;

/**
 * CRUD operations for the MediaAsset table, which stores metadata
 * (file path, MIME type, size, alt text) for every uploaded image/file.
 *
 * Also provides a helper to link an asset to a CmsItem record.
 */
class MediaAssetRepository implements IMediaAssetRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Finds a media asset by ID.
     *
     * @param int $mediaAssetId
     * @return MediaAsset|null
     */
    public function findById(int $mediaAssetId): ?MediaAsset
    {
        $stmt = $this->pdo->prepare('SELECT * FROM MediaAsset WHERE MediaAssetId = :mediaAssetId');
        $stmt->execute([':mediaAssetId' => $mediaAssetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? MediaAsset::fromRow($result) : null;
    }

    /**
     * Batch-fetches multiple media assets in a single query, keyed by ID for easy lookup.
     *
     * @param int[] $ids
     * @return array<int, MediaAsset> Keyed by MediaAssetId
     */
    public function findByIds(array $ids): array
    {
        $ids = array_filter($ids, fn(int $id) => $id > 0);
        if ($ids === []) {
            return [];
        }

        // Build numbered placeholders (:id0, :id1, ...) for a safe IN clause
        $paramKeys = [];
        $paramValues = [];
        foreach (array_values($ids) as $index => $id) {
            $key = ':id' . $index;
            $paramKeys[] = $key;
            $paramValues[$key] = $id;
        }
        $sql = "SELECT * FROM MediaAsset WHERE MediaAssetId IN (" . implode(',', $paramKeys) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($paramValues);
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
                VALUES (:filePath, :originalFileName, :mimeType, :fileSizeBytes, :altText)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':filePath'         => $data['FilePath'],
            ':originalFileName' => $data['OriginalFileName'],
            ':mimeType'         => $data['MimeType'],
            ':fileSizeBytes'    => $data['FileSizeBytes'],
            ':altText'          => $data['AltText'] ?? '',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Partially updates a media asset -- only columns present in $data are changed.
     *
     * @return bool False if $data contained no recognised columns, true on successful execute.
     */
    public function update(int $mediaAssetId, array $data): bool
    {
        // Dynamically build SET clause from whichever fields the caller provided
        $fields = [];
        $params = [':mediaAssetId' => $mediaAssetId];

        foreach (['FilePath', 'OriginalFileName', 'MimeType', 'FileSizeBytes', 'AltText'] as $field) {
            if (array_key_exists($field, $data)) {
                $key = ':' . lcfirst($field);
                $fields[] = "$field = $key";
                $params[$key] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE MediaAsset SET ' . implode(', ', $fields) . ' WHERE MediaAssetId = :mediaAssetId';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Deletes a media asset record.
     */
    public function delete(int $mediaAssetId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM MediaAsset WHERE MediaAssetId = :mediaAssetId');
        return $stmt->execute([':mediaAssetId' => $mediaAssetId]);
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
        $stmt = $this->pdo->prepare('UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId');
        return $stmt->execute([':mediaAssetId' => $mediaAssetId, ':cmsItemId' => $cmsItemId]);
    }

    /**
     * Returns all media assets ordered by newest first.
     *
     * @return MediaAsset[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM MediaAsset ORDER BY CreatedAtUtc DESC');
        $stmt->execute([]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([MediaAsset::class, 'fromRow'], $rows);
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;

/**
 * CRUD operations for the MediaAsset table, which stores metadata
 * (file path, MIME type, size, alt text) for every uploaded image/file.
 *
 * Also provides a helper to link an asset to a CmsItem record.
 */
class MediaAssetRepository extends BaseRepository implements IMediaAssetRepository
{
    /**
     * Finds a media asset by ID.
     *
     * @param int $mediaAssetId
     * @return MediaAsset|null
     */
    public function findById(int $mediaAssetId): ?MediaAsset
    {
        return $this->fetchOne(
            'SELECT * FROM MediaAsset WHERE MediaAssetId = :mediaAssetId',
            [':mediaAssetId' => $mediaAssetId],
            fn(array $row) => MediaAsset::fromRow($row),
        );
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

        $inClause = $this->buildInClause(array_values($ids), 'id');
        $sql = "SELECT * FROM MediaAsset WHERE MediaAssetId IN ({$inClause['placeholders']})";

        $stmt = $this->execute($sql, $inClause['params']);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

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
        return $this->executeInsert(
            'INSERT INTO MediaAsset (FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText)
            VALUES (:filePath, :originalFileName, :mimeType, :fileSizeBytes, :altText)',
            [
                ':filePath'         => $data['FilePath'],
                ':originalFileName' => $data['OriginalFileName'],
                ':mimeType'         => $data['MimeType'],
                ':fileSizeBytes'    => $data['FileSizeBytes'],
                ':altText'          => $data['AltText'] ?? '',
            ],
        );
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
        $this->execute($sql, $params);

        return true;
    }

    /**
     * Deletes a media asset record.
     */
    public function delete(int $mediaAssetId): bool
    {
        $this->execute(
            'DELETE FROM MediaAsset WHERE MediaAssetId = :mediaAssetId',
            [':mediaAssetId' => $mediaAssetId],
        );

        return true;
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
        $this->execute(
            'UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId',
            [':mediaAssetId' => $mediaAssetId, ':cmsItemId' => $cmsItemId],
        );

        return true;
    }

    /**
     * Returns all media assets ordered by newest first.
     *
     * @return MediaAsset[]
     */
    public function findAll(): array
    {
        return $this->fetchAll(
            'SELECT * FROM MediaAsset ORDER BY CreatedAtUtc DESC',
            [],
            fn(array $row) => MediaAsset::fromRow($row),
        );
    }

}

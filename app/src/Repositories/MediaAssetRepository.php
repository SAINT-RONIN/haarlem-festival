<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MediaAsset;
use App\Repositories\Interfaces\IMediaAssetRepository;

class MediaAssetRepository extends BaseRepository implements IMediaAssetRepository
{
    public function findById(int $mediaAssetId): ?MediaAsset
    {
        return $this->fetchOne(
            'SELECT * FROM MediaAsset WHERE MediaAssetId = :mediaAssetId',
            [':mediaAssetId' => $mediaAssetId],
            fn(array $row) => MediaAsset::fromRow($row),
        );
    }

    /** @return array<int, MediaAsset> keyed by MediaAssetId */
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

    // $data keys: FilePath, OriginalFileName, MimeType, FileSizeBytes, AltText
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

    // Only writes columns present in $data.
    public function update(int $mediaAssetId, array $data): bool
    {
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

    public function delete(int $mediaAssetId): bool
    {
        $this->execute(
            'DELETE FROM MediaAsset WHERE MediaAssetId = :mediaAssetId',
            [':mediaAssetId' => $mediaAssetId],
        );

        return true;
    }

    public function linkToCmsItem(int $mediaAssetId, int $cmsItemId): bool
    {
        $this->execute(
            'UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId',
            [':mediaAssetId' => $mediaAssetId, ':cmsItemId' => $cmsItemId],
        );

        return true;
    }

    // Excludes ticket PDFs (MIME not starting with "image/") from the CMS image library.
    public function findAll(): array
    {
        return $this->fetchAll(
            "SELECT * FROM MediaAsset WHERE MimeType LIKE 'image/%' ORDER BY CreatedAtUtc DESC",
            [],
            fn(array $row) => MediaAsset::fromRow($row),
        );
    }
}

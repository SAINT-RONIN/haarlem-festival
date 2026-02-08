<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
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
     */
    public function findById(int $mediaAssetId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM MediaAsset WHERE MediaAssetId = ?');
        $stmt->execute([$mediaAssetId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
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
}


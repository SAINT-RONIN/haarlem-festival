<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\CmsItem;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Repositories\Interfaces\ICmsRepository;
use PDO;

class CmsRepository implements ICmsRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return CmsPage[]
     */
    public function findPages(array $filters = []): array
    {
        $includeLastUpdated = (bool)($filters['includeLastUpdated'] ?? false);

        $select = $includeLastUpdated
            ? '
                SELECT cp.*, MAX(ci.UpdatedAtUtc) AS UpdatedAtUtc
                FROM CmsPage cp
                LEFT JOIN CmsSection cs ON cp.CmsPageId = cs.CmsPageId
                LEFT JOIN CmsItem ci ON cs.CmsSectionId = ci.CmsSectionId
            '
            : 'SELECT cp.* FROM CmsPage cp';

        $conditions = [];
        $params = [];

        if (isset($filters['cmsPageId'])) {
            $conditions[] = 'cp.CmsPageId = :cmsPageId';
            $params['cmsPageId'] = (int)$filters['cmsPageId'];
        }

        if (isset($filters['slug']) && is_string($filters['slug']) && $filters['slug'] !== '') {
            $conditions[] = 'cp.Slug = :slug';
            $params['slug'] = $filters['slug'];
        }

        $sql = $select;
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($includeLastUpdated) {
            $sql .= ' GROUP BY cp.CmsPageId ORDER BY UpdatedAtUtc DESC';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([CmsPage::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findSections(array $filters = []): array
    {
        $sql = 'SELECT * FROM CmsSection WHERE 1 = 1';
        $params = [];

        if (isset($filters['cmsPageId'])) {
            $sql .= ' AND CmsPageId = :cmsPageId';
            $params['cmsPageId'] = (int)$filters['cmsPageId'];
        }

        if (isset($filters['sectionKey']) && is_string($filters['sectionKey']) && $filters['sectionKey'] !== '') {
            $sql .= ' AND SectionKey = :sectionKey';
            $params['sectionKey'] = $filters['sectionKey'];
        }

        $sql .= ' ORDER BY CmsSectionId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([CmsSection::class, 'fromRow'], $rows);
    }

    public function findItems(array $filters = []): array
    {
        $sql = '
            SELECT ci.*
            FROM CmsItem ci
            INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
            INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
            WHERE 1 = 1
        ';
        $params = [];

        if (isset($filters['cmsItemId'])) {
            $sql .= ' AND ci.CmsItemId = :cmsItemId';
            $params['cmsItemId'] = (int)$filters['cmsItemId'];
        }

        if (isset($filters['cmsSectionId'])) {
            $sql .= ' AND ci.CmsSectionId = :cmsSectionId';
            $params['cmsSectionId'] = (int)$filters['cmsSectionId'];
        }

        if (isset($filters['cmsPageId'])) {
            $sql .= ' AND cs.CmsPageId = :cmsPageId';
            $params['cmsPageId'] = (int)$filters['cmsPageId'];
        }

        if (isset($filters['sectionKey']) && is_string($filters['sectionKey']) && $filters['sectionKey'] !== '') {
            $sql .= ' AND cs.SectionKey = :sectionKey';
            $params['sectionKey'] = $filters['sectionKey'];
        }

        $sql .= ' ORDER BY ci.CmsItemId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([CmsItem::class, 'fromRow'], $rows);
    }

    public function updateItem(int $cmsItemId, array $data): bool
    {
        $fields = [];
        $params = [];

        if (array_key_exists('TextValue', $data)) {
            $fields[] = 'TextValue = :textValue';
            $params['textValue'] = $data['TextValue'];
        }

        if (array_key_exists('HtmlValue', $data)) {
            $fields[] = 'HtmlValue = :htmlValue';
            $params['htmlValue'] = $data['HtmlValue'];
        }

        if ($fields === []) {
            return false;
        }

        $params['cmsItemId'] = $cmsItemId;
        $sql = 'UPDATE CmsItem SET ' . implode(', ', $fields) . ' WHERE CmsItemId = :cmsItemId';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId');
        return $stmt->execute(['mediaAssetId' => $mediaAssetId, 'cmsItemId' => $cmsItemId]);
    }
}

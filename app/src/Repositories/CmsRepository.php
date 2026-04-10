<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CmsItem;
use App\DTOs\Domain\Filters\CmsItemFilter;
use App\Models\CmsPage;
use App\DTOs\Domain\Filters\CmsPageFilter;
use App\Models\CmsSection;
use App\DTOs\Domain\Filters\CmsSectionFilter;
use App\Repositories\Interfaces\ICmsRepository;

// Low-level CRUD for CmsPage, CmsSection, and CmsItem.
// Higher-level content resolution lives in CmsContentRepository.
class CmsRepository extends BaseRepository implements ICmsRepository
{
    public function findPages(CmsPageFilter $filter): array
    {
        // When last-updated info is requested, join through sections -> items
        $select = $filter->includeLastUpdated
            ? '
                SELECT cp.*, MAX(ci.UpdatedAtUtc) AS UpdatedAtUtc
                FROM CmsPage cp
                LEFT JOIN CmsSection cs ON cp.CmsPageId = cs.CmsPageId
                LEFT JOIN CmsItem ci ON cs.CmsSectionId = ci.CmsSectionId
            '
            : 'SELECT cp.* FROM CmsPage cp';

        $conditions = [];
        $params = [];

        if ($filter->cmsPageId !== null) {
            $conditions[] = 'cp.CmsPageId = :cmsPageId';
            $params['cmsPageId'] = $filter->cmsPageId;
        }

        if ($filter->slug !== null && $filter->slug !== '') {
            $conditions[] = 'cp.Slug = :slug';
            $params['slug'] = $filter->slug;
        }

        $sql = $select;
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($filter->includeLastUpdated) {
            $sql .= ' GROUP BY cp.CmsPageId ORDER BY UpdatedAtUtc DESC';
        }

        return $this->fetchAll($sql, $params, fn(array $row) => CmsPage::fromRow($row));
    }

    public function findSections(CmsSectionFilter $filter): array
    {
        $sql = 'SELECT * FROM CmsSection WHERE 1 = 1';
        $params = [];

        if ($filter->cmsPageId !== null) {
            $sql .= ' AND CmsPageId = :cmsPageId';
            $params['cmsPageId'] = $filter->cmsPageId;
        }

        if ($filter->sectionKey !== null && $filter->sectionKey !== '') {
            $sql .= ' AND SectionKey = :sectionKey';
            $params['sectionKey'] = $filter->sectionKey;
        }

        $sql .= ' ORDER BY CmsSectionId ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => CmsSection::fromRow($row));
    }

    // Joins CmsItem -> CmsSection -> CmsPage so callers can filter by page/section in one call.
    public function findItems(CmsItemFilter $filter): array
    {
        $sql = '
            SELECT ci.*
            FROM CmsItem ci
            INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
            INNER JOIN CmsPage cp ON cs.CmsPageId = cp.CmsPageId
            WHERE 1 = 1
        ';
        $params = [];

        if ($filter->cmsItemId !== null) {
            $sql .= ' AND ci.CmsItemId = :cmsItemId';
            $params['cmsItemId'] = $filter->cmsItemId;
        }

        if ($filter->cmsSectionId !== null) {
            $sql .= ' AND ci.CmsSectionId = :cmsSectionId';
            $params['cmsSectionId'] = $filter->cmsSectionId;
        }

        if ($filter->cmsPageId !== null) {
            $sql .= ' AND cs.CmsPageId = :cmsPageId';
            $params['cmsPageId'] = $filter->cmsPageId;
        }

        if ($filter->sectionKey !== null && $filter->sectionKey !== '') {
            $sql .= ' AND cs.SectionKey = :sectionKey';
            $params['sectionKey'] = $filter->sectionKey;
        }

        $sql .= ' ORDER BY ci.CmsItemId ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => CmsItem::fromRow($row));
    }

    // Only writes columns present in $data; returns false when $data has no recognised keys.
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
        $this->execute($sql, $params);

        return true;
    }

    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool
    {
        $this->execute(
            'UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId',
            ['mediaAssetId' => $mediaAssetId, 'cmsItemId' => $cmsItemId],
        );

        return true;
    }

    public function findPageBySlug(string $slug): ?CmsPage
    {
        return $this->fetchOne(
            'SELECT * FROM CmsPage WHERE Slug = :slug LIMIT 1',
            ['slug' => $slug],
            fn(array $row) => CmsPage::fromRow($row),
        );
    }

    public function insertSection(int $cmsPageId, string $sectionKey): int
    {
        return $this->executeInsert(
            'INSERT INTO CmsSection (CmsPageId, SectionKey) VALUES (:cmsPageId, :sectionKey)',
            ['cmsPageId' => $cmsPageId, 'sectionKey' => $sectionKey],
        );
    }

    // Upsert TEXT item by section + key (ON DUPLICATE KEY UPDATE).
    public function upsertCmsTextItem(int $cmsSectionId, string $itemKey, string $textValue): void
    {
        $this->execute(
            'INSERT INTO CmsItem (CmsSectionId, ItemKey, ItemType, TextValue)
             VALUES (:sectionId, :key, :type, :value)
             ON DUPLICATE KEY UPDATE TextValue = VALUES(TextValue)',
            [
                'sectionId' => $cmsSectionId,
                'key' => $itemKey,
                'type' => 'TEXT',
                'value' => $textValue,
            ],
        );
    }
}

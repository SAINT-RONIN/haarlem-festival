<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\CmsItem;
use App\Models\CmsItemFilter;
use App\Models\CmsPage;
use App\Models\CmsPageFilter;
use App\Models\CmsSection;
use App\Models\CmsSectionFilter;
use App\Repositories\Interfaces\ICmsRepository;
use PDO;

/**
 * Low-level data access for the three core CMS tables: CmsPage, CmsSection, and CmsItem.
 *
 * Provides filtered reads and partial updates. Higher-level content resolution
 * (slug lookup, caching, media-asset hydration) lives in CmsContentRepository.
 */
class CmsRepository implements ICmsRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Finds CMS pages with optional filtering by ID or slug.
     *
     * When includeLastUpdated is set, the query joins through CmsSection -> CmsItem
     * and aggregates MAX(UpdatedAtUtc) so the CMS dashboard can show when a page was
     * last edited.
     *
     * @return CmsPage[]
     */
    public function findPages(CmsPageFilter $filter): array
    {
        // When last-updated info is requested, join page -> sections -> items to find
        // the most recent CmsItem edit timestamp across the whole page
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([CmsPage::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Finds CMS sections, optionally scoped to a page and/or section key.
     *
     * @return CmsSection[]
     */
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([CmsSection::class, 'fromRow'], $rows);
    }

    /**
     * Finds CMS items with optional filtering by item ID, section, page, or section key.
     *
     * The query joins CmsItem -> CmsSection -> CmsPage so callers can filter items
     * by page-level or section-level criteria in a single call.
     *
     * @return CmsItem[]
     */
    public function findItems(CmsItemFilter $filter): array
    {
        // Join up to CmsPage so we can filter by page ID or section key
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([CmsItem::class, 'fromRow'], $rows);
    }

    /**
     * Partially updates a CMS item's text or HTML value.
     * Only columns present in $data are written; others are left untouched.
     *
     * @param array<string, mixed> $data Allowed keys: TextValue, HtmlValue
     * @return bool False when $data had no recognised keys; true on execute.
     */
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

    /**
     * Replaces (or clears) the media asset linked to a CMS item.
     */
    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE CmsItem SET MediaAssetId = :mediaAssetId WHERE CmsItemId = :cmsItemId');
        return $stmt->execute(['mediaAssetId' => $mediaAssetId, 'cmsItemId' => $cmsItemId]);
    }
}

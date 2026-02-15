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

    public function getPageBySlug(string $slug): ?CmsPage
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsPage WHERE Slug = ?');
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? CmsPage::fromRow($result) : null;
    }

    /**
     * @return CmsSection[]
     */
    public function getSectionsByPageId(int $cmsPageId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsSection WHERE CmsPageId = :cmsPageId ORDER BY CmsSectionId ASC');
        $stmt->bindValue(':cmsPageId', $cmsPageId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([CmsSection::class, 'fromRow'], $rows);
    }

    /**
     * @return CmsItem[]
     */
    public function getItemsBySectionId(int $cmsSectionId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsItem WHERE CmsSectionId = :cmsSectionId ORDER BY CmsItemId ASC');
        $stmt->bindValue(':cmsSectionId', $cmsSectionId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([CmsItem::class, 'fromRow'], $rows);
    }

    /**
     * @return CmsItem[]
     */
    public function getItemsBySectionKey(int $cmsPageId, string $sectionKey): array
    {
        $sql = '
            SELECT ci.* 
            FROM CmsItem ci
            INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
            WHERE cs.CmsPageId = :cmsPageId AND cs.SectionKey = :sectionKey
            ORDER BY ci.CmsItemId ASC
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cmsPageId', $cmsPageId, PDO::PARAM_INT);
        $stmt->bindValue(':sectionKey', $sectionKey, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([CmsItem::class, 'fromRow'], $rows);
    }

    /**
     * Retrieves all CMS pages with last update time.
     *
     * Returns arrays for this aggregate query (includes MAX UpdatedAtUtc).
     *
     * @return array<int, array{CmsPageId: int, Title: string, Slug: string, UpdatedAtUtc: ?string}>
     */
    public function findAllPages(): array
    {
        $sql = '
            SELECT cp.*, 
                   MAX(ci.UpdatedAtUtc) as UpdatedAtUtc
            FROM CmsPage cp
            LEFT JOIN CmsSection cs ON cp.CmsPageId = cs.CmsPageId
            LEFT JOIN CmsItem ci ON cs.CmsSectionId = ci.CmsSectionId
            GROUP BY cp.CmsPageId
            ORDER BY UpdatedAtUtc DESC
        ';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves a CMS page by ID.
     */
    public function getPageById(int $cmsPageId): ?CmsPage
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsPage WHERE CmsPageId = ?');
        $stmt->execute([$cmsPageId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? CmsPage::fromRow($result) : null;
    }

    /**
     * Retrieves a CMS item by ID.
     */
    public function getItemById(int $cmsItemId): ?CmsItem
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsItem WHERE CmsItemId = ?');
        $stmt->execute([$cmsItemId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? CmsItem::fromRow($result) : null;
    }

    /**
     * Updates a CMS item's text/html content.
     *
     * @param int $cmsItemId The item ID
     * @param array $data Keys: TextValue, HtmlValue
     */
    public function updateItem(int $cmsItemId, array $data): bool
    {
        $fields = [];
        $values = [];

        if (array_key_exists('TextValue', $data)) {
            $fields[] = 'TextValue = ?';
            $values[] = $data['TextValue'];
        }

        if (array_key_exists('HtmlValue', $data)) {
            $fields[] = 'HtmlValue = ?';
            $values[] = $data['HtmlValue'];
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $cmsItemId;
        $sql = 'UPDATE CmsItem SET ' . implode(', ', $fields) . ' WHERE CmsItemId = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Updates a CMS item's media asset reference.
     */
    public function updateItemMediaAsset(int $cmsItemId, ?int $mediaAssetId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE CmsItem SET MediaAssetId = ? WHERE CmsItemId = ?');
        return $stmt->execute([$mediaAssetId, $cmsItemId]);
    }
}

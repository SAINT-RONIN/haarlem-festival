<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\ICmsRepository;
use PDO;

class CmsRepository implements ICmsRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getPageBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsPage WHERE Slug = ?');
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getSectionsByPageId(int $cmsPageId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsSection WHERE CmsPageId = :cmsPageId ORDER BY CmsSectionId ASC');
        $stmt->bindValue(':cmsPageId', $cmsPageId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsBySectionId(int $cmsSectionId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsItem WHERE CmsSectionId = :cmsSectionId ORDER BY CmsItemId ASC');
        $stmt->bindValue(':cmsSectionId', $cmsSectionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all CMS pages.
     *
     * @return array Array of page records
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
}

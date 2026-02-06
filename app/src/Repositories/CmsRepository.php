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
}

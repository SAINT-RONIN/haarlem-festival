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
        $stmt = $this->pdo->prepare('SELECT * FROM CmsSection WHERE CmsPageId = ? ORDER BY CmsSectionId ASC');
        $stmt->execute([$cmsPageId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsBySectionId(int $cmsSectionId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM CmsItem WHERE CmsSectionId = ? ORDER BY CmsItemId ASC');
        $stmt->execute([$cmsSectionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemsBySectionKey(int $cmsPageId, string $sectionKey): array
    {
        $sql = '
            SELECT ci.* 
            FROM CmsItem ci
            INNER JOIN CmsSection cs ON ci.CmsSectionId = cs.CmsSectionId
            WHERE cs.CmsPageId = ? AND cs.SectionKey = ?
            ORDER BY ci.CmsItemId ASC
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cmsPageId, $sectionKey]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


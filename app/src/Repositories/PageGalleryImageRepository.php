<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PageGalleryImage;
use App\Repositories\Interfaces\IPageGalleryImageRepository;
use PDO;

/**
 * Read-only access to the PageGalleryImage table.
 *
 * Page gallery images are tied to a CMS page (rather than an event) and
 * support an optional ImageType filter, ordered by SortOrder.
 */
class PageGalleryImageRepository implements IPageGalleryImageRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Returns gallery images for a CMS page, optionally filtered by image type, ordered by SortOrder.
     *
     * @return PageGalleryImage[]
     */
    public function findByPageId(int $cmsPageId, ?string $imageType = null): array
    {
        $sql = 'SELECT * FROM PageGalleryImage WHERE CmsPageId = :cmsPageId';
        $params = ['cmsPageId' => $cmsPageId];

        if ($imageType !== null) {
            $sql .= ' AND ImageType = :imageType';
            $params['imageType'] = $imageType;
        }

        $sql .= ' ORDER BY SortOrder ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([PageGalleryImage::class, 'fromRow'], $rows);
    }
}

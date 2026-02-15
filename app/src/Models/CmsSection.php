<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `CmsSection` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class CmsSection
{
    /*
     * Purpose: Groups CMS items into logical sections within a page
     * for organized content management.
     */

    public function __construct(
        public readonly int    $cmsSectionId,
        public readonly int    $cmsPageId,
        public readonly string $sectionKey,
    )
    {
    }

    /**
     * Creates a CmsSection instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            cmsSectionId: (int)$row['CmsSectionId'],
            cmsPageId: (int)$row['CmsPageId'],
            sectionKey: (string)$row['SectionKey'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'CmsSectionId' => $this->cmsSectionId,
            'CmsPageId' => $this->cmsPageId,
            'SectionKey' => $this->sectionKey,
        ];
    }
}

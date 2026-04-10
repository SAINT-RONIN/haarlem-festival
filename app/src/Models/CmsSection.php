<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the CmsSection table.
 *
 * Groups related CMS items within a page (e.g., 'hero', 'intro', 'pricing') for structured
 * content editing.
 */
final readonly class CmsSection
{
    /*
     * Purpose: Groups CMS items into logical sections within a page
     * for organized content management.
     */

    public function __construct(
        public int    $cmsSectionId,
        public int    $cmsPageId,
        public string $sectionKey,
    ) {}

    /**
     * Creates a CmsSection instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            cmsSectionId: (int) ($row['CmsSectionId'] ?? throw new \InvalidArgumentException('Missing required field: CmsSectionId')),
            cmsPageId: (int) ($row['CmsPageId'] ?? throw new \InvalidArgumentException('Missing required field: CmsPageId')),
            sectionKey: (string) ($row['SectionKey'] ?? throw new \InvalidArgumentException('Missing required field: SectionKey')),
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

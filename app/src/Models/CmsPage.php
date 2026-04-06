<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the CmsPage table.
 *
 * Each CMS page corresponds to a public-facing page (home, jazz, storytelling) whose content
 * is editable through the CMS dashboard.
 */
final readonly class CmsPage
{
    /*
     * Purpose: Holds CMS page metadata (slug, title) for managing
     * editable website pages.
     */

    public function __construct(
        public int                   $cmsPageId,
        public string                $slug,
        public string                $title,
        public ?\DateTimeImmutable   $updatedAtUtc = null,
    ) {
    }

    /**
     * Creates a CmsPage instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        $rawUpdatedAt = $row['UpdatedAtUtc'] ?? null;
        $updatedAtUtc = (is_string($rawUpdatedAt) && $rawUpdatedAt !== '')
            ? new \DateTimeImmutable($rawUpdatedAt)
            : null;

        return new self(
            cmsPageId: (int)$row['CmsPageId'],
            slug: (string)$row['Slug'],
            title: (string)$row['Title'],
            updatedAtUtc: $updatedAtUtc,
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'CmsPageId' => $this->cmsPageId,
            'Slug' => $this->slug,
            'Title' => $this->title,
        ];
    }
}

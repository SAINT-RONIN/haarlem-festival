<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `CmsPage` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class CmsPage
{
    /*
     * Purpose: Holds CMS page metadata (slug, title) for managing
     * editable website pages.
     */

    public function __construct(
        public readonly int                   $cmsPageId,
        public readonly string                $slug,
        public readonly string                $title,
        public readonly ?\DateTimeImmutable   $updatedAtUtc = null,
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

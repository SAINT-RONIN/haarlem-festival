<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CmsItemType;

/**
 * Represents a single row from the `CmsItem` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class CmsItem
{
    /*
     * Purpose: Holds individual CMS content items (text, HTML, or media)
     * that belong to a CMS section for dynamic page content.
     */

    public function __construct(
        public readonly int                $cmsItemId,
        public readonly int                $cmsSectionId,
        public readonly string             $itemKey,
        public readonly CmsItemType        $itemType,
        public readonly ?string            $textValue,
        public readonly ?string            $htmlValue,
        public readonly ?int               $mediaAssetId,
        public readonly \DateTimeImmutable $updatedAtUtc,
    ) {
    }

    /**
     * Creates a CmsItem instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            cmsItemId: (int)$row['CmsItemId'],
            cmsSectionId: (int)$row['CmsSectionId'],
            itemKey: (string)$row['ItemKey'],
            itemType: CmsItemType::from($row['ItemType']),
            textValue: $row['TextValue'] ?? null,
            htmlValue: $row['HtmlValue'] ?? null,
            mediaAssetId: isset($row['MediaAssetId']) ? (int)$row['MediaAssetId'] : null,
            updatedAtUtc: new \DateTimeImmutable($row['UpdatedAtUtc']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'CmsItemId' => $this->cmsItemId,
            'CmsSectionId' => $this->cmsSectionId,
            'ItemKey' => $this->itemKey,
            'ItemType' => $this->itemType->value,
            'TextValue' => $this->textValue,
            'HtmlValue' => $this->htmlValue,
            'MediaAssetId' => $this->mediaAssetId,
            'UpdatedAtUtc' => $this->updatedAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}

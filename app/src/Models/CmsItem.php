<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CmsItemType;

/**
 * Represents a row in the CmsItem table.
 *
 * The atomic unit of CMS content — a single editable field (heading, text block, image, URL)
 * within a section.
 */
final readonly class CmsItem
{
    /*
     * Purpose: Holds individual CMS content items (text, HTML, or media)
     * that belong to a CMS section for dynamic page content.
     */

    public function __construct(
        public int                $cmsItemId,
        public int                $cmsSectionId,
        public string             $itemKey,
        public CmsItemType        $itemType,
        public ?string            $textValue,
        public ?string            $htmlValue,
        public ?int               $mediaAssetId,
        public \DateTimeImmutable $updatedAtUtc,
    ) {
    }

    /**
     * Creates a CmsItem instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            cmsItemId: (int)($row['CmsItemId'] ?? throw new \InvalidArgumentException('Missing required field: CmsItemId')),
            cmsSectionId: (int)($row['CmsSectionId'] ?? throw new \InvalidArgumentException('Missing required field: CmsSectionId')),
            itemKey: (string)($row['ItemKey'] ?? throw new \InvalidArgumentException('Missing required field: ItemKey')),
            itemType: CmsItemType::from($row['ItemType'] ?? throw new \InvalidArgumentException('Missing required field: ItemType')),
            textValue: $row['TextValue'] ?? null,
            htmlValue: $row['HtmlValue'] ?? null,
            mediaAssetId: isset($row['MediaAssetId']) ? (int)$row['MediaAssetId'] : null,
            updatedAtUtc: new \DateTimeImmutable($row['UpdatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: UpdatedAtUtc')),
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

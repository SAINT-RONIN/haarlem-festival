<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the Artist table.
 *
 * Artists are performers associated with Jazz events, displayed on both the jazz landing
 * page and individual artist detail pages.
 */
final readonly class Artist
{
    /*
     * Purpose: Holds artist information (name, style, bio) for performers
     * appearing at festival events.
     */

    public function __construct(
        public int                $artistId,
        public string             $name,
        public string             $style,
        public string             $bioHtml,
        public ?int               $imageAssetId,
        public bool               $isActive,
        public \DateTimeImmutable $createdAtUtc,
    ) {
    }

    /**
     * Creates an Artist instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            artistId: (int)($row['ArtistId'] ?? throw new \InvalidArgumentException('Missing required field: ArtistId')),
            name: (string)($row['Name'] ?? throw new \InvalidArgumentException('Missing required field: Name')),
            style: (string)($row['Style'] ?? throw new \InvalidArgumentException('Missing required field: Style')),
            bioHtml: (string)($row['BioHtml'] ?? throw new \InvalidArgumentException('Missing required field: BioHtml')),
            imageAssetId: isset($row['ImageAssetId']) ? (int)$row['ImageAssetId'] : null,
            isActive: (bool)($row['IsActive'] ?? throw new \InvalidArgumentException('Missing required field: IsActive')),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: CreatedAtUtc')),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'ArtistId' => $this->artistId,
            'Name' => $this->name,
            'Style' => $this->style,
            'BioHtml' => $this->bioHtml,
            'ImageAssetId' => $this->imageAssetId,
            'IsActive' => $this->isActive,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}

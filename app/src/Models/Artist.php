<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Artist` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
final class Artist
{
    /*
     * Purpose: Holds artist information (name, style, bio) for performers
     * appearing at festival events.
     */

    public function __construct(
        public readonly int                $artistId,
        public readonly string             $name,
        public readonly string             $style,
        public readonly string             $bioHtml,
        public readonly ?int               $imageAssetId,
        public readonly bool               $isActive,
        public readonly \DateTimeImmutable $createdAtUtc,
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

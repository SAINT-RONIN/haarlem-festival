<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Artist` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Artist
{
    /*
     * Purpose: Holds artist information (name, style, bio) for performers
     * appearing at festival events.
     */

    public function __construct(
        public int $artistId,
        public string $name,
        public string $style,
        public string $bioHtml,
        public ?int $imageAssetId,
        public bool $isActive,
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
            artistId: (int) $row['ArtistId'],
            name: (string) $row['Name'],
            style: (string) $row['Style'],
            bioHtml: (string) $row['BioHtml'],
            imageAssetId: isset($row['ImageAssetId']) ? (int) $row['ImageAssetId'] : null,
            isActive: (bool) $row['IsActive'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
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


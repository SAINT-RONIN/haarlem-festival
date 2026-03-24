<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IDanceRepository;
use PDO;

class DanceRepository implements IDanceRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getAllArtists(): array
    {
        $sql = "
            SELECT ArtistId, Name
            FROM Artist
            ORDER BY Name ASC
        ";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function (array $row): array {
            $name = (string) $row['Name'];

            return [
                'artistId' => (int) $row['ArtistId'],
                'name' => $name,
                'slug' => $this->slugify($name),
            ];
        }, $rows);
    }

    public function getArtistBySlug(string $slug): ?array
    {
        foreach ($this->getAllArtists() as $artist) {
            if ($artist['slug'] === $slug) {
                return $artist;
            }
        }

        return null;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}
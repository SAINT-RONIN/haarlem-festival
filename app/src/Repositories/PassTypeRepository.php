<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\PassType;
use App\Repositories\Interfaces\IPassTypeRepository;
use PDO;

class PassTypeRepository implements IPassTypeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /** @return PassType[] */
    public function findByEventType(int $eventTypeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM PassType WHERE EventTypeId = :eventTypeId AND IsActive = 1 ORDER BY PassTypeId ASC'
        );
        $stmt->execute([':eventTypeId' => $eventTypeId]);

        return array_map(
            fn(array $row) => PassType::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IHistoryRepository;
use PDO;

class HistoryRepository implements IHistoryRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }
}

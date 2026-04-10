<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\RepositoryException;

// Shared PDO helpers for all repositories. Every query is wrapped in try-catch
// so callers get RepositoryException instead of raw PDOException.
abstract class BaseRepository
{
    public function __construct(
        protected readonly \PDO $pdo,
    ) {}

    protected function execute(string $sql, array $params = []): \PDOStatement
    {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);

            return $statement;
        } catch (\PDOException $error) {
            throw new RepositoryException(
                'Database query failed: ' . $error->getMessage(),
                (int) $error->getCode(),
                $error,
            );
        }
    }

    // Wraps prepare + execute + lastInsertId so a connection drop between
    // execute and lastInsertId cannot leak a raw PDOException.
    protected function executeInsert(string $sql, array $params = []): int
    {
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute($params);

            return (int) $this->pdo->lastInsertId();
        } catch (\PDOException $error) {
            throw new RepositoryException(
                'Database insert failed: ' . $error->getMessage(),
                (int) $error->getCode(),
                $error,
            );
        }
    }

    protected function fetchAll(string $sql, array $params, callable $mapper): array
    {
        $statement = $this->execute($sql, $params);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return array_map($mapper, $rows);
    }

    protected function fetchOne(string $sql, array $params, callable $mapper): mixed
    {
        $statement = $this->execute($sql, $params);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $mapper($row);
    }

    // Builds named placeholders and matching param array for an IN (...) clause.
    //
    //   $in = $this->buildInClause([10, 20, 30], 'sessionId');
    //   // $in['placeholders'] => ':sessionId0,:sessionId1,:sessionId2'
    //   // $in['params']       => [':sessionId0' => 10, ':sessionId1' => 20, ':sessionId2' => 30]
    protected function buildInClause(array $ids, string $prefix = 'id'): array
    {
        $placeholders = [];
        $params = [];

        foreach (array_values($ids) as $index => $value) {
            $key = ':' . $prefix . $index;
            $placeholders[] = $key;
            $params[$key] = $value;
        }

        return [
            'placeholders' => implode(',', $placeholders),
            'params' => $params,
        ];
    }

    /**
     * @template T of object
     * @param array<int, T> $items
     * @return array<int|string, array<int, T>>
     */
    protected function groupByKey(array $items, string $keyProperty): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $key = $item->{$keyProperty};
            $grouped[$key][] = $item;
        }

        return $grouped;
    }
}

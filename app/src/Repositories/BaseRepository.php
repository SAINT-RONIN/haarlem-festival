<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\RepositoryException;

/**
 * Shared base for all PDO-backed repositories.
 *
 * Provides protected helpers that wrap prepare/execute in a try-catch so
 * every repository query is guarded against \PDOException without
 * repeating the same boilerplate in 30+ classes.
 */
abstract class BaseRepository
{
    public function __construct(
        protected readonly \PDO $pdo,
    ) {
    }

    /**
     * Prepares and executes a SQL statement.
     *
     * @param string               $sql    The SQL query with named placeholders.
     * @param array<string, mixed> $params Bound parameter values.
     * @return \PDOStatement The executed statement (for callers that need fetchColumn, rowCount, etc.).
     * @throws RepositoryException When the database rejects the query.
     */
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

    /**
     * Executes a SELECT and maps every returned row through a callable.
     *
     * @param string               $sql    The SQL query with named placeholders.
     * @param array<string, mixed> $params Bound parameter values.
     * @param callable(array<string, mixed>): mixed $mapper Row-to-object converter, e.g. fn(array $row) => Model::fromRow($row).
     * @return array<int, mixed> List of mapped objects.
     * @throws RepositoryException When the database rejects the query.
     */
    protected function fetchAll(string $sql, array $params, callable $mapper): array
    {
        $statement = $this->execute($sql, $params);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return array_map($mapper, $rows);
    }

    /**
     * Executes a SELECT and maps the first row through a callable.
     *
     * @param string               $sql    The SQL query with named placeholders.
     * @param array<string, mixed> $params Bound parameter values.
     * @param callable(array<string, mixed>): mixed $mapper Row-to-object converter.
     * @return mixed|null The mapped object, or null when no row is found.
     * @throws RepositoryException When the database rejects the query.
     */
    protected function fetchOne(string $sql, array $params, callable $mapper): mixed
    {
        $statement = $this->execute($sql, $params);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $mapper($row);
    }

    /**
     * Builds named placeholders and a matching parameter array for an IN clause.
     *
     * Usage:
     *   $in = $this->buildInClause([10, 20, 30], 'sessionId');
     *   // $in['placeholders'] => ':sessionId0,:sessionId1,:sessionId2'
     *   // $in['params']       => [':sessionId0' => 10, ':sessionId1' => 20, ':sessionId2' => 30]
     *
     * @param array<int, int|string> $ids    The values that go into the IN (...) list.
     * @param string                 $prefix Placeholder name prefix (default 'id').
     * @return array{placeholders: string, params: array<string, int|string>}
     */
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
     * Groups a flat list of objects by a shared property value.
     *
     * Useful after fetching labels/prices for multiple sessions —
     * each item gets filed under its parent ID.
     *
     * @template T of object
     * @param array<int, T> $items       The flat list to group.
     * @param string         $keyProperty The public property whose value becomes the group key.
     * @return array<int|string, array<int, T>> Grouped items keyed by the property value.
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

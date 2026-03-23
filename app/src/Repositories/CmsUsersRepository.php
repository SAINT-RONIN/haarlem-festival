<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAccount;
use App\Models\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;
use PDO;

/**
 * Handles all UserAccount read/write operations for the CMS users section.
 */
class CmsUsersRepository implements ICmsUsersRepository
{
    private const SORT_COLUMNS = [
        'username'   => 'ua.Username',
        'email'      => 'ua.Email',
        'name'       => 'ua.FirstName',
        'role'       => 'ur.RoleName',
        'registered' => 'ua.RegisteredAtUtc',
    ];

    private const SORT_DIRS = ['asc', 'desc'];

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * Returns all users with their role name, with optional filtering, search, and sort.
     *
     * @return UserWithRole[]
     */
    public function findUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array {
        [$sql, $params] = $this->buildListQuery($roleFilter, $search, $sortBy, $sortDir);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(
            fn(array $row) => UserWithRole::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    public function findById(int $id): ?UserAccount
    {
        $stmt = $this->pdo->prepare('SELECT * FROM UserAccount WHERE UserAccountId = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? UserAccount::fromRow($row) : null;
    }

    public function createUser(
        string $username,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int {
        $sql = '
            INSERT INTO UserAccount
                (UserRoleId, Username, Email, PasswordHash, PasswordSalt, FirstName, LastName, IsEmailConfirmed, IsActive)
            VALUES
                (:roleId, :username, :email, :passwordHash, NULL, :firstName, :lastName, 0, 1)
        ';

        $this->pdo->prepare($sql)->execute([
            ':roleId'       => $roleId,
            ':username'     => $username,
            ':email'        => $email,
            ':passwordHash' => $passwordHash,
            ':firstName'    => $firstName,
            ':lastName'     => $lastName,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateUser(
        int $id,
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void {
        $sql = '
            UPDATE UserAccount
            SET UserRoleId = :roleId,
                Username   = :username,
                Email      = :email,
                FirstName  = :firstName,
                LastName   = :lastName,
                UpdatedAtUtc = NOW()
            WHERE UserAccountId = :id
        ';

        $this->pdo->prepare($sql)->execute([
            ':roleId'    => $roleId,
            ':username'  => $username,
            ':email'     => $email,
            ':firstName' => $firstName,
            ':lastName'  => $lastName,
            ':id'        => $id,
        ]);
    }

    public function updateUserPassword(int $id, string $passwordHash): void
    {
        $sql = 'UPDATE UserAccount SET PasswordHash = :hash, UpdatedAtUtc = NOW() WHERE UserAccountId = :id';

        $this->pdo->prepare($sql)->execute([':hash' => $passwordHash, ':id' => $id]);
    }

    public function deleteUser(int $id): void
    {
        $sql = 'UPDATE UserAccount SET IsActive = 0, UpdatedAtUtc = NOW() WHERE UserAccountId = :id';

        $this->pdo->prepare($sql)->execute([':id' => $id]);
    }

    public function existsByUsername(string $username): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM UserAccount WHERE Username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);

        return $stmt->fetchColumn() !== false;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM UserAccount WHERE Email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        return $stmt->fetchColumn() !== false;
    }

    public function existsByUsernameExcluding(string $username, int $excludeId): bool
    {
        $sql  = 'SELECT 1 FROM UserAccount WHERE Username = :username AND UserAccountId != :excludeId LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username, ':excludeId' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    public function existsByEmailExcluding(string $email, int $excludeId): bool
    {
        $sql  = 'SELECT 1 FROM UserAccount WHERE Email = :email AND UserAccountId != :excludeId LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email, ':excludeId' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildListQuery(
        ?int $roleFilter,
        ?string $search,
        string $sortBy,
        string $sortDir,
    ): array {
        $sql    = $this->buildListSelect();
        $params = [];

        if ($roleFilter !== null) {
            $sql .= ' AND ua.UserRoleId = :roleId';
            $params[':roleId'] = $roleFilter;
        }

        if ($search !== null && $search !== '') {
            $sql .= ' AND (ua.Username LIKE :search OR ua.Email LIKE :search'
                  . ' OR ua.FirstName LIKE :search OR ua.LastName LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY ' . $this->resolveSortColumn($sortBy) . ' ' . $this->resolveSortDir($sortDir);

        return [$sql, $params];
    }

    private function buildListSelect(): string
    {
        return '
            SELECT
                ua.UserAccountId,
                ua.Username,
                ua.Email,
                ua.FirstName,
                ua.LastName,
                ua.IsActive,
                ua.RegisteredAtUtc,
                ur.RoleName
            FROM UserAccount ua
            LEFT JOIN UserRole ur ON ua.UserRoleId = ur.UserRoleId
            WHERE 1 = 1
        ';
    }

    private function resolveSortColumn(string $sortBy): string
    {
        return self::SORT_COLUMNS[$sortBy] ?? 'ua.RegisteredAtUtc';
    }

    private function resolveSortDir(string $sortDir): string
    {
        return in_array(strtolower($sortDir), self::SORT_DIRS, true) ? strtoupper($sortDir) : 'DESC';
    }
}

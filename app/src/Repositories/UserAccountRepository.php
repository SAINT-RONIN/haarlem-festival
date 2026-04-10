<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAccount;
use App\Repositories\Interfaces\IUserAccountRepository;

class UserAccountRepository extends BaseRepository implements IUserAccountRepository
{
    // Login lookup: matches either username or email, active users only.
    public function findByUsernameOrEmail(string $login): ?UserAccount
    {
        return $this->fetchOne(
            'SELECT * FROM UserAccount
            WHERE (Username = :loginUsername OR Email = :loginEmail)
            AND IsActive = 1',
            ['loginUsername' => $login, 'loginEmail' => $login],
            fn(array $row) => UserAccount::fromRow($row),
        );
    }

    public function findByEmail(string $email): ?UserAccount
    {
        return $this->fetchOne(
            'SELECT * FROM UserAccount WHERE Email = :email AND IsActive = 1',
            ['email' => $email],
            fn(array $row) => UserAccount::fromRow($row),
        );
    }

    public function findById(int $id): ?UserAccount
    {
        return $this->fetchOne(
            'SELECT * FROM UserAccount WHERE UserAccountId = :id',
            ['id' => $id],
            fn(array $row) => UserAccount::fromRow($row),
        );
    }

    public function existsByUsername(string $username): bool
    {
        $stmt = $this->execute(
            'SELECT COUNT(*) FROM UserAccount WHERE Username = :username',
            ['username' => $username],
        );

        return (int) $stmt->fetchColumn() > 0;
    }

    public function existsByEmail(string $email): bool
    {
        $stmt = $this->execute(
            'SELECT COUNT(*) FROM UserAccount WHERE Email = :email',
            ['email' => $email],
        );

        return (int) $stmt->fetchColumn() > 0;
    }

    // PasswordSalt is NULL -- app uses bcrypt/argon2 (salt is embedded in the hash).
    // Column exists for legacy compatibility.
    public function createUser(
        string $username,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int {
        return $this->executeInsert(
            'INSERT INTO UserAccount
                (UserRoleId, Username, Email, PasswordHash, PasswordSalt, FirstName, LastName, IsEmailConfirmed, IsActive)
            VALUES
                (:roleId, :username, :email, :passwordHash, NULL, :firstName, :lastName, 0, 1)',
            [
                ':roleId'       => $roleId,
                ':username'     => $username,
                ':email'        => $email,
                ':passwordHash' => $passwordHash,
                ':firstName'    => $firstName,
                ':lastName'     => $lastName,
            ],
        );
    }

    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $this->execute(
            'UPDATE UserAccount SET PasswordHash = :hash, UpdatedAtUtc = NOW() WHERE UserAccountId = :id',
            [':hash' => $passwordHash, ':id' => $userId],
        );
    }

    public function updateProfileInfo(
        int $userId,
        string $email,
        string $firstName,
        string $lastName,
        ?int $profilePictureAssetId = null,
    ): void {
        $this->execute(
            'UPDATE UserAccount
             SET Email = :email, FirstName = :firstName, LastName = :lastName,
                 ProfilePictureAssetId = :profilePictureAssetId, UpdatedAtUtc = NOW()
             WHERE UserAccountId = :id',
            [
                ':email' => $email,
                ':firstName' => $firstName,
                ':lastName' => $lastName,
                ':profilePictureAssetId' => $profilePictureAssetId,
                ':id' => $userId,
            ],
        );
    }

    public function updateUser(
        int $id,
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void {
        $this->execute(
            'UPDATE UserAccount
            SET UserRoleId = :roleId, Username = :username, Email = :email,
                FirstName = :firstName, LastName = :lastName, UpdatedAtUtc = NOW()
            WHERE UserAccountId = :id',
            [
                ':roleId'    => $roleId,
                ':username'  => $username,
                ':email'     => $email,
                ':firstName' => $firstName,
                ':lastName'  => $lastName,
                ':id'        => $id,
            ],
        );
    }

    // Soft-delete preserves order/FK history.
    public function deleteUser(int $id): void
    {
        $this->execute(
            'UPDATE UserAccount SET IsActive = 0, UpdatedAtUtc = NOW() WHERE UserAccountId = :id',
            [':id' => $id],
        );
    }

    public function reactivateUser(int $id): void
    {
        $this->execute(
            'UPDATE UserAccount SET IsActive = 1, UpdatedAtUtc = NOW() WHERE UserAccountId = :id',
            [':id' => $id],
        );
    }

    // For edit-form uniqueness checks (excludes the user being edited).
    public function emailExistsForOtherUser(string $email, int $excludeUserId): bool
    {
        $stmt = $this->execute(
            'SELECT 1 FROM UserAccount WHERE Email = :email AND UserAccountId != :userId LIMIT 1',
            [':email' => $email, ':userId' => $excludeUserId],
        );

        return $stmt->fetch() !== false;
    }
}

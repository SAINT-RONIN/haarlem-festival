<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAccount;
use App\Repositories\Interfaces\IUserAccountRepository;
use PDO;

/**
 * Repository for UserAccount database operations.
 *
 * Handles all SQL queries related to user accounts including
 * authentication lookups, registration, and password updates.
 */
class UserAccountRepository implements IUserAccountRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Finds an active user matching by either username or email.
     * Used at login time so users can sign in with either credential.
     */
    public function findByUsernameOrEmail(string $login): ?UserAccount
    {
        $sql = '
            SELECT * FROM UserAccount 
            WHERE (Username = :loginUsername OR Email = :loginEmail) 
            AND IsActive = 1
        ';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'loginUsername' => $login,
            'loginEmail' => $login,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? UserAccount::fromRow($row) : null;
    }

    /**
     * Finds a user by email address.
     */
    public function findByEmail(string $email): ?UserAccount
    {
        $sql = 'SELECT * FROM UserAccount WHERE Email = :email AND IsActive = 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? UserAccount::fromRow($row) : null;
    }

    /**
     * Finds a user by ID.
     */
    public function findById(int $id): ?UserAccount
    {
        $sql = 'SELECT * FROM UserAccount WHERE UserAccountId = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? UserAccount::fromRow($row) : null;
    }

    /**
     * Checks if a username is already taken.
     */
    public function existsByUsername(string $username): bool
    {
        $sql = 'SELECT COUNT(*) FROM UserAccount WHERE Username = :username';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Checks if an email is already registered.
     */
    public function existsByEmail(string $email): bool
    {
        $sql = 'SELECT COUNT(*) FROM UserAccount WHERE Email = :email';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Creates a new user account. PasswordSalt is always NULL because the app uses
     * bcrypt/argon2 (salt is embedded in the hash). The column exists for legacy compatibility.
     *
     * @return int The new user's ID
     */
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

    /**
     * Updates a user's password hash.
     */
    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $sql = 'UPDATE UserAccount SET PasswordHash = :hash, UpdatedAtUtc = NOW() WHERE UserAccountId = :id';

        $this->pdo->prepare($sql)->execute([':hash' => $passwordHash, ':id' => $userId]);
    }

    /**
     * Updates a user account's profile fields (does not change the password).
     */
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

    /**
     * Soft-deletes a user by setting IsActive = 0 (preserves order/FK history).
     */
    public function deleteUser(int $id): void
    {
        $sql = 'UPDATE UserAccount SET IsActive = 0, UpdatedAtUtc = NOW() WHERE UserAccountId = :id';

        $this->pdo->prepare($sql)->execute([':id' => $id]);
    }
}

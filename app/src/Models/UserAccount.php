<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `UserAccount` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class UserAccount
{
    /*
     * Purpose: Stores user account data including credentials,
     * profile info, and account status for authentication.
     */

    public function __construct(
        public readonly int                $userAccountId,
        public readonly int                $userRoleId,
        public readonly string             $username,
        public readonly string             $email,
        public readonly string             $passwordHash,
        public readonly ?string            $passwordSalt, // Nullable for Argon2id (salt embedded in hash)
        public readonly string             $firstName,
        public readonly string             $lastName,
        public readonly ?int               $profilePictureAssetId,
        public readonly bool               $isEmailConfirmed,
        public readonly bool               $isActive,
        public readonly \DateTimeImmutable $registeredAtUtc,
        public readonly \DateTimeImmutable $updatedAtUtc,
    )
    {
    }

    /**
     * Creates a UserAccount instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            userAccountId: (int)$row['UserAccountId'],
            userRoleId: (int)$row['UserRoleId'],
            username: (string)$row['Username'],
            email: (string)$row['Email'],
            passwordHash: (string)$row['PasswordHash'],
            passwordSalt: isset($row['PasswordSalt']) ? (string)$row['PasswordSalt'] : null,
            firstName: (string)$row['FirstName'],
            lastName: (string)$row['LastName'],
            profilePictureAssetId: isset($row['ProfilePictureAssetId']) ? (int)$row['ProfilePictureAssetId'] : null,
            isEmailConfirmed: (bool)$row['IsEmailConfirmed'],
            isActive: (bool)$row['IsActive'],
            registeredAtUtc: new \DateTimeImmutable($row['RegisteredAtUtc']),
            updatedAtUtc: new \DateTimeImmutable($row['UpdatedAtUtc']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'UserAccountId' => $this->userAccountId,
            'UserRoleId' => $this->userRoleId,
            'Username' => $this->username,
            'Email' => $this->email,
            'PasswordHash' => $this->passwordHash,
            'PasswordSalt' => $this->passwordSalt,
            'FirstName' => $this->firstName,
            'LastName' => $this->lastName,
            'ProfilePictureAssetId' => $this->profilePictureAssetId,
            'IsEmailConfirmed' => $this->isEmailConfirmed,
            'IsActive' => $this->isActive,
            'RegisteredAtUtc' => $this->registeredAtUtc->format('Y-m-d H:i:s'),
            'UpdatedAtUtc' => $this->updatedAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}

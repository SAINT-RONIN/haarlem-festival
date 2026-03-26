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
    ) {
    }

    /**
     * Creates a UserAccount instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            userAccountId:         (int)($row['UserAccountId'] ?? throw new \InvalidArgumentException('Missing required field: UserAccountId')),
            userRoleId:            (int)($row['UserRoleId'] ?? throw new \InvalidArgumentException('Missing required field: UserRoleId')),
            username:              (string)($row['Username'] ?? throw new \InvalidArgumentException('Missing required field: Username')),
            email:                 (string)($row['Email'] ?? throw new \InvalidArgumentException('Missing required field: Email')),
            passwordHash:          (string)($row['PasswordHash'] ?? throw new \InvalidArgumentException('Missing required field: PasswordHash')),
            passwordSalt:          isset($row['PasswordSalt']) ? (string)$row['PasswordSalt'] : null,
            firstName:             (string)($row['FirstName'] ?? throw new \InvalidArgumentException('Missing required field: FirstName')),
            lastName:              (string)($row['LastName'] ?? throw new \InvalidArgumentException('Missing required field: LastName')),
            profilePictureAssetId: isset($row['ProfilePictureAssetId']) ? (int)$row['ProfilePictureAssetId'] : null,
            isEmailConfirmed:      (bool)($row['IsEmailConfirmed'] ?? throw new \InvalidArgumentException('Missing required field: IsEmailConfirmed')),
            isActive:              (bool)($row['IsActive'] ?? throw new \InvalidArgumentException('Missing required field: IsActive')),
            registeredAtUtc:       new \DateTimeImmutable($row['RegisteredAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: RegisteredAtUtc')),
            updatedAtUtc:          new \DateTimeImmutable($row['UpdatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: UpdatedAtUtc')),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     *
     * @return array<string, mixed>
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

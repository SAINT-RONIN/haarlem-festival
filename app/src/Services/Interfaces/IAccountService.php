<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Models\UserAccount;

/**
 * Contract for user account management operations.
 * Covers profile updates, email changes with confirmation, and password changes.
 */
interface IAccountService
{
    /**
     * Retrieves the current user account by ID.
     *
     * @throws \App\Exceptions\AccountException When user is not found
     */
    public function getCurrentUser(int $userId): UserAccount;

    /**
     * @return array<string, string> Field name => error message
     */
    public function validateProfileUpdate(UpdateProfileFormData $data, int $currentUserId): array;
    public function updateProfile(UpdateProfileFormData $data, int $userId): void;
    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): void;
}


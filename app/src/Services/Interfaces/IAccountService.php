<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\ValidationException;
use App\Models\UserAccount;
use Throwable;

interface IAccountService
{
    /**
     * @throws \App\Exceptions\AccountException When user is not found
     */
    public function getCurrentUser(int $userId): UserAccount;

    /**
     * @return array<string, string> Field name => error message
     */
    public function validateProfileUpdate(UpdateProfileFormData $data, int $currentUserId): array;

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function updateProfile(UpdateProfileFormData $data, UserAccount $user): void;

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): void;
}


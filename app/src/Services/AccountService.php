<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\ValidationException;
use App\Utils\PasswordHasher;
use App\Models\UserAccount;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Services\Interfaces\IAccountService;
use App\Infrastructure\Interfaces\IEmailService;

class AccountService implements IAccountService
{
    public function __construct(
        private IUserAccountRepository $userRepository,
        private IEmailService $emailService,
    ) {}


    public function getCurrentUser(int $userId): UserAccount
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new AccountException('User account not found.');
        }
        return $user;
    }

    /** @return array<string, string> */
    public function validateProfileUpdate(UpdateProfileFormData $data, int $currentUserId): array
    {
        $errors = [];
        $errors = array_merge($errors, $this->validateEmail($data->email, $currentUserId));
        $errors = array_merge($errors, $this->validateName($data->firstName, $data->lastName));
        return $errors;
    }

    /** @return array<string, string> */
    private function validateEmail(string $email, int $excludeUserId): array
    {
        $errors = [];

        if (empty($email)) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->userRepository->emailExistsForOtherUser($email, $excludeUserId)) {
            $errors['email'] = 'This email is already in use by another account.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private function validateName(string $firstName, string $lastName): array
    {
        $errors = [];
        $errors = array_merge($errors, $this->validateNameField($firstName, 'firstName'));
        $errors = array_merge($errors, $this->validateNameField($lastName, 'lastName'));
        return $errors;
    }

    private function validateNameField(string $name, string $fieldName): array
    {
        $errors = [];

        if (empty($name)) {
            $errors[$fieldName] = ucfirst($fieldName) . ' is required.';
        } elseif (strlen($name) < 2) {
            $errors[$fieldName] = ucfirst($fieldName) . ' must be at least 2 characters.';
        } elseif (strlen($name) > 100) {
            $errors[$fieldName] = ucfirst($fieldName) . ' must not exceed 100 characters.';
        }

        return $errors;
    }


    public function updateProfile(UpdateProfileFormData $data, int $userId): void
    {
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new AccountException('User not found.');
        }
        $nameChanged = trim($data->firstName) !== $user->firstName || trim($data->lastName) !== $user->lastName;
        $profilePictureChanged = $data->profilePictureAssetId !== null && $data->profilePictureAssetId !== $user->profilePictureAssetId;
        $passwordChanged = !empty($data->newPassword);
        try {
            // Update basic profile info
            $this->userRepository->updateProfileInfo(
                userId: $userId,
                email: trim($data->email),
                firstName: trim($data->firstName),
                lastName: trim($data->lastName),
                profilePictureAssetId: $data->profilePictureAssetId,
            );
            // Send account update confirmation for any changes
            $changes = [];
            if ($nameChanged) {
                $changes[] = 'profile name';
            }
            if ($profilePictureChanged) {
                $changes[] = 'profile picture';
            }
            if ($passwordChanged) {
                $changes[] = 'password';
            }
            if (!empty($changes)) {
                $changeDescription = implode(' and ', $changes);
                $userName = trim($data->firstName . ' ' . $data->lastName);
                $this->emailService->sendAccountUpdateConfirmationEmail($user->email, $userName, $changeDescription);
            }
        } catch (\Throwable $error) {
            throw new AccountException('Failed to update profile: ' . $error->getMessage(), 0, $error);
        }
    }


    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): array
    {
        //Validate data
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            return [
                'success' => false,
                'errors' => ['general' => 'User not found']
            ];
        }
        $errors = $this->validatePassword($user, $currentPassword, $newPassword, $confirmPassword);
        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        // Update password hash in database
        $passwordHash = PasswordHasher::hash($newPassword);
        $this->userRepository->updatePasswordHash($userId, $passwordHash);

        // Send account update confirmation email
        $userName = trim($user->firstName . ' ' . $user->lastName);
        $this->emailService->sendAccountUpdateConfirmationEmail($user->email, $userName, 'password');
        return [
            'success' => true,
            'errors' => [],
        ];
    }

    private function validatePassword(UserAccount $user, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        $errors = [];

        if (!PasswordHasher::verify($currentPassword, $user->passwordHash)) {
            $errors['currentPassword'] = 'Current password is incorrect';
        }
        if (mb_strlen($newPassword) < 8) {
            $errors['newPassword'] = 'Password must be at least 8 characters';
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirmPassword'] = 'Passwords do not match';
        }

        return $errors;
    }

}


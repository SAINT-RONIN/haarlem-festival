<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\ValidationException;
use App\Exceptions\EmailDeliveryException;
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
        $user = $this->userRepository->findActiveById($userId);
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
        $errors = array_merge($errors, $this->validateNameField($firstName, 'firstName', 'First name'));
        $errors = array_merge($errors, $this->validateNameField($lastName, 'lastName', 'Last name'));
        return $errors;
    }

    private function validateNameField(string $name, string $fieldName, string $shownField): array
    {
        $errors = [];

        if (empty($name)) {
            $errors[$fieldName] = ucfirst($shownField) . ' is required.';
        } elseif (mb_strlen($name) < 2) {
            $errors[$fieldName] = ucfirst($shownField) . ' must be at least 2 characters.';
        } elseif (mb_strlen($name) > 100) {
            $errors[$fieldName] = ucfirst($shownField) . ' must not exceed 100 characters.';
        }

        return $errors;
    }

    private function getPreviousPdoException(\Throwable $error): ?\PDOException
    {
        if ($error instanceof \PDOException) {
            return $error;
        }

        $previous = $error->getPrevious();

        return $previous instanceof \PDOException ? $previous : null;
    }

    private function isDuplicateEmailError(\Throwable $error): bool
    {
        $pdoError = $this->getPreviousPdoException($error);

        if ($pdoError === null) {
            return false;
        }

        $errorInfo = $pdoError->errorInfo;

        $sqlState = $errorInfo[0] ?? null;
        $driverCode = $errorInfo[1] ?? null;
        $message = strtolower($errorInfo[2] ?? $pdoError->getMessage());

        return $sqlState === '23000'
            && (int) $driverCode === 1062
            && (
                str_contains($message, 'uq_useraccount_email')
                || str_contains($message, 'email')
            );
    }


    public function updateProfile(UpdateProfileFormData $data, int $userId): void
    {
        $user = $this->userRepository->findActiveById($userId);
        if ($user === null) {
            throw new AccountException('User not found.');
        }
        $nameChanged = trim($data->firstName) !== $user->firstName || trim($data->lastName) !== $user->lastName;
        $oldEmail = $user->email;
        $newEmail = trim($data->email);
        $emailChanged = trim($data->email) !== $user->email;

        $profilePictureChanged = $data->profilePictureAssetId !== null && $data->profilePictureAssetId !== $user->profilePictureAssetId;
        if (!$nameChanged && !$emailChanged && !$profilePictureChanged) {
            return;
        }
        try {
            // Update basic profile info
            $this->userRepository->updateProfileInfo(
                userId: $userId,
                email: trim($data->email),
                firstName: trim($data->firstName),
                lastName: trim($data->lastName),
                profilePictureAssetId: $data->profilePictureAssetId ?? $user->profilePictureAssetId,
            );
        } catch (\PDOException $error) {
            if ($this->isDuplicateEmailError($error)) {
                throw new ValidationException([
                    'email' => 'This email is already in use by another account.',
                ], 0, $error);
            }
            throw new AccountException('Could not update profile. Please try again later.', 0, $error);
        } catch (\Throwable $error) {
            throw new AccountException('Could not update profile. Please try again later.', 0, $error);
        }

        // Send account update confirmation for any changes
        $changes = [];
        if ($emailChanged) {
            $changes[] = 'email address';
        }

        if ($nameChanged) {
            $changes[] = 'profile name';
        }

        if ($profilePictureChanged) {
            $changes[] = 'profile picture';
        }

        if (!empty($changes)) {
            try {
                $changeDescription = implode(' and ', $changes);
                $userName = trim($data->firstName . ' ' . $data->lastName);

                $this->emailService->sendAccountUpdateConfirmationEmail(
                    $oldEmail,
                    $userName,
                    $changeDescription,
                );

                if ($emailChanged) {
                    $this->emailService->sendAccountUpdateConfirmationEmail(
                        $newEmail,
                        $userName,
                        $changeDescription,
                    );
                }
            } catch (EmailDeliveryException $error) {
                // Ideally log this, but do not fail the profile update.
            } catch (\Throwable $error) {
                // Ideally log this, but do not fail the profile update.
            }
        }
    }


    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): void
    {
        //Validate data
        $user = $this->userRepository->findActiveById($userId);
        if ($user === null) {
            throw new AccountException('User not found.');
        }
        $errors = $this->validatePassword($user, $currentPassword, $newPassword, $confirmPassword);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        // Update password hash in database
        try {
            $passwordHash = PasswordHasher::hash($newPassword);
            $this->userRepository->updatePasswordHash($userId, $passwordHash);
        } catch (\Throwable $error) {
            throw new AccountException('Could not update password. Please try again later.', 0, $error);
        }

        // Send account update confirmation email
        try {
            $userName = trim($user->firstName . ' ' . $user->lastName);
            $this->emailService->sendAccountUpdateConfirmationEmail($user->email, $userName, 'password');
        } catch (EmailDeliveryException $error) {
            // Ideally log this, but do not fail the password update.
        } catch (\Throwable $error) {
            // Ideally log this, but do not fail the password update.
        }
    }

    private function validatePassword(UserAccount $user, string $currentPassword, string $newPassword, string $confirmPassword): array
    {
        $errors = [];

        if (!PasswordHasher::verify($currentPassword, $user->passwordHash)) {
            $errors['currentPassword'] = 'Current password is incorrect';
        }
        if ($newPassword === '') {
            $errors['newPassword'] = 'New password is required';
        } elseif (mb_strlen($newPassword) < 8) {
            $errors['newPassword'] = 'Password must be at least 8 characters';
        }

        if ($confirmPassword === '') {
            $errors['confirmPassword'] = 'Please confirm your new password';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirmPassword'] = 'Passwords do not match';
        }

        if ($currentPassword === $newPassword) {
            $errors['newPassword'] = 'New password must be different from current password';
        }

        return $errors;
    }

}


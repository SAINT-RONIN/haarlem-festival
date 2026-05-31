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

    /**
     * @throws AccountException
     */
    public function updateProfile(UpdateProfileFormData $data, int $userId): void
    {
        $user = $this->getCurrentUser($userId);
        $email = trim($data->email);
        $firstName = trim($data->firstName);
        $lastName = trim($data->lastName);
        $changes = $this->detectProfileChanges($user, $email, $firstName, $lastName, $data->profilePictureAssetId);

        if (empty($changes)) {
            return;
        }

        $this->updateProfileInfoOrFail(data: $data, user: $user, userId: $userId, email: $email, firstName: $firstName, lastName: $lastName);
        $this->sendProfileUpdateConfirmationEmail(userId: $userId, oldEmail: $user->email, newEmail: $email, userName: trim($firstName . ' ' . $lastName), changes: $changes,);
    }

    /**
     * @throws AccountException
     */
    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): void {
        $user = $this->getCurrentUser($userId);
        $errors = $this->validatePassword($user, $currentPassword, $newPassword, $confirmPassword);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $this->updatePasswordHashOrFail($userId, $newPassword);
        $this->sendPasswordUpdateConfirmationEmail($user, $userId);
    }

    public function getCurrentUser(int $userId): UserAccount
    {
        $user = $this->userRepository->findActiveById($userId);
        if ($user === null) {
            throw new AccountException('User account not found.');
        }
        return $user;
    }

    /**
     * @return list<string>
     */
    private function detectProfileChanges(UserAccount $user, string $email, string $firstName, string $lastName, ?int $profilePictureAssetId,): array {
        $changes = [];

        if ($email !== $user->email) {
            $changes[] = 'email address';
        }

        if ($firstName !== $user->firstName || $lastName !== $user->lastName) {
            $changes[] = 'profile name';
        }

        if ($profilePictureAssetId !== null && $profilePictureAssetId !== $user->profilePictureAssetId) {
            $changes[] = 'profile picture';
        }

        return $changes;
    }

    /**
     * @throws AccountException
     */
    private function updateProfileInfoOrFail(UpdateProfileFormData $data, UserAccount $user, int $userId, string $email, string $firstName, string $lastName,): void {
        try {
            $this->userRepository->updateProfileInfo(userId: $userId, email: $email, firstName: $firstName, lastName: $lastName, profilePictureAssetId: $data->profilePictureAssetId ?? $user->profilePictureAssetId,);
        } catch (\Throwable $error) {
            if ($this->isDuplicateEmailError($error)) {
                throw new ValidationException(['email' => 'This email is already in use by another account.',], 0, $error);
            }
            throw new AccountException('Could not update profile. Please try again later.', 0, $error);
        }
    }

    /**
     * @param list<string> $changes
     */
    private function sendProfileUpdateConfirmationEmail(int $userId, string $oldEmail, string $newEmail, string $userName, array $changes,): void {
        try {
            $changeDescription = implode(' and ', $changes);

            $this->emailService->sendAccountUpdateConfirmationEmail($oldEmail, $userName, $changeDescription,);

            if ($newEmail !== $oldEmail) {
                $this->emailService->sendAccountUpdateConfirmationEmail($newEmail, $userName, $changeDescription,);
            }
        } catch (EmailDeliveryException $error) {
            $this->logEmailDeliveryFailure('profile update confirmation', $userId, $error,
            );
        } catch (\Throwable $error) {
            $this->logUnexpectedEmailFailure('profile update confirmation', $userId, $error,);
        }
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


    private function logEmailDeliveryFailure(string $emailType, int $userId, \Throwable $error): void
    {
        error_log('Failed to send ' . $emailType . ' email for user ID ' . $userId . ': ' . $error->getMessage());
    }

    private function logUnexpectedEmailFailure(string $emailType, int $userId, \Throwable $error): void
    {
        error_log('Unexpected error while sending ' . $emailType . ' email for user ID ' . $userId . ': ' . $error->getMessage());
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

    private function updatePasswordHashOrFail(int $userId, string $newPassword): void
    {
        try {
            $passwordHash = PasswordHasher::hash($newPassword);
            $this->userRepository->updatePasswordHash($userId, $passwordHash);
        } catch (\Throwable $error) {
            throw new AccountException('Could not update password. Please try again later.', 0, $error);
        }
    }

    private function sendPasswordUpdateConfirmationEmail(UserAccount $user, int $userId): void
    {
        try {
            $userName = trim($user->firstName . ' ' . $user->lastName);
            $this->emailService->sendAccountUpdateConfirmationEmail($user->email, $userName, 'password',);
        } catch (EmailDeliveryException $error) {
            $this->logEmailDeliveryFailure('password update confirmation', $userId, $error,);
        } catch (\Throwable $error) {
            $this->logUnexpectedEmailFailure('password update confirmation', $userId, $error,);
        }
    }

}


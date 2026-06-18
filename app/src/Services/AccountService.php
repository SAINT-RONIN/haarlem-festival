<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Domain\Account\UpdateProfileFormData;
use App\Exceptions\AccountException;
use App\Exceptions\SmtpNotConfiguredException;
use App\Exceptions\ValidationException;
use App\Exceptions\EmailDeliveryException;
use App\Services\Interfaces\IMediaAssetService;
use App\Utils\PasswordHasher;
use App\Models\UserAccount;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Services\Interfaces\IAccountService;
use App\Infrastructure\Interfaces\IEmailService;
use App\Helpers\UserValidationHelper;

class AccountService implements IAccountService
{
    public function __construct(
        private IUserAccountRepository $userRepository,
        private IEmailService $emailService,
        private IMediaAssetService $mediaAssetService,
    ) {}

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function updateProfile(UpdateProfileFormData $data, UserAccount $user): void
    {
        // UserAccount is readonly, so "merge" means creating a new model instance with updated profile fields instead of mutating the existing object
        $updatedUser = $this->mergeProfileDataIntoUser($user, $data);

        // keep the original user for change detection and for sending email to the old address if the email was changed.
        $changes = $this->detectProfileChanges($user, $updatedUser);

        if (empty($changes)) {
            return;
        }

        try{
            // persist the already merged user model
            $this->updateProfileInfo($updatedUser);
            $this->sendProfileUpdateConfirmationEmail(userId: $user->userAccountId, oldEmail: $user->email, newEmail: $updatedUser->email, userName: trim($updatedUser->firstName . ' ' . $updatedUser->lastName), changes: $changes,);
        } catch (ValidationException | AccountException $e ){
            //if a new profile picture was uploaded but the update fails, delete the uploaded picture to avoid orphaned files
            if ($data->profilePictureAssetId !== null && $data->profilePictureAssetId !== $user->profilePictureAssetId) {
                try {
                    $this->mediaAssetService->deleteAsset($data->profilePictureAssetId);
                } catch (\Throwable $cleanupError) {
                    error_log('Failed to delete orphaned profile picture asset ID ' . $data->profilePictureAssetId . ': ' . $cleanupError->getMessage());
                }
            }
            throw $e;
        }
    }

    private function mergeProfileDataIntoUser(UserAccount $user, UpdateProfileFormData $data): UserAccount
    {
        return $user->withUpdatedProfile(
            email: trim($data->email),
            firstName: trim($data->firstName),
            lastName: trim($data->lastName),
            profilePictureAssetId: $data->profilePictureAssetId ?? $user->profilePictureAssetId,
        );
    }

    /**
     * @throws ValidationException
     * @throws AccountException
     */
    public function updatePassword(string $currentPassword, string $newPassword, string $confirmPassword, int $userId): void {
        $user = $this->getCurrentUser($userId);
        $errors = $this->validatePassword($user, $currentPassword, $newPassword, $confirmPassword);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        try {
            $passwordHash = PasswordHasher::hash($newPassword);
            $this->userRepository->updatePasswordHash($userId, $passwordHash);
        } catch (\Throwable $error) {
            throw new AccountException('Could not update password. Please try again later.', 0, $error);
        }

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
    private function detectProfileChanges(UserAccount $oldUser, UserAccount $updatedUser): array
    {
        $changes = [];

        if ($updatedUser->email !== $oldUser->email) {
            $changes[] = 'email address';
        }

        if ($updatedUser->firstName !== $oldUser->firstName || $updatedUser->lastName !== $oldUser->lastName) {
            $changes[] = 'name';
        }

        if ($updatedUser->profilePictureAssetId !== $oldUser->profilePictureAssetId) {
            $changes[] = 'profile picture';
        }

        return $changes;
    }

    /**
     * @throws AccountException
     */
    private function updateProfileInfo(UserAccount $user): void {
        try {
            $this->userRepository->updateProfileInfo(userId: $user->userAccountId, email: $user->email, firstName: $user->firstName, lastName: $user->lastName, profilePictureAssetId: $user->profilePictureAssetId,
            );
        } catch (\Throwable $error) {
            if ($this->isDuplicateEmailConstraintError($error)) {
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
        } catch (SmtpNotConfiguredException $error) {
            $this->logEmailDeliveryFailure('profile update confirmation', $userId, $error);
        } catch (\Throwable $error) {
            $this->logUnexpectedEmailFailure('profile update confirmation', $userId, $error,);
        }
    }

    /** @return array<string, string> */
    public function validateProfileUpdate(UpdateProfileFormData $data, int $currentUserId): array
    {
        $errors = [];
        $errors = array_merge($errors, $this->validateEmail($data->email, $currentUserId));
        $errors = array_merge($errors, UserValidationHelper::checkNames($data->firstName, $data->lastName));
        return $errors;
    }

    /** @return array<string, string> */
    private function validateEmail(string $email, int $excludeUserId): array
    {
        $errors = [];
        $email = trim($email);
        $formatError = UserValidationHelper::checkEmail($email);

        if ($formatError !== null) {
            $errors['email'] = $formatError;
            return $errors;
        }

        if ($this->userRepository->emailExistsForOtherUser($email, $excludeUserId)) {
            $errors['email'] = 'This email is already in use by another account.';
        }

        return $errors;
    }

    private function findPdoException(\Throwable $error): ?\PDOException
    {
        // walk through the exception chain until the original PDOException is found
        for ($current = $error; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof \PDOException) {
                return $current;
            }
        }

        return null;
    }

    private function isDuplicateEmailConstraintError(\Throwable $error): bool
    {
        // repository/PDO errors can be wrapped inside another exception
        // we only need the original PDOException to inspect the SQL error details
        $pdoError = $this->findPdoException($error);

        if ($pdoError === null) {
            return false;
        }
        $errorInfo = $pdoError->errorInfo;

        $sqlState = $errorInfo[0] ?? null;
        $driverCode = (int) ($errorInfo[1] ?? 0);
        $message = strtolower((string) ($errorInfo[2] ?? $pdoError->getMessage()));

        // MySQL duplicate key error: SQLSTATE 23000 = integrity constraint violation, driver code 1062 = duplicate entry
        $isDuplicateKeyError = $sqlState === '23000' && $driverCode === 1062;

        // make sure this duplicate key error is specifically about the email column/constraint, not about another unique field such as username.
        $isEmailConstraint = str_contains($message, 'uq_useraccount_email') || str_contains($message, 'email');

        return $isDuplicateKeyError && $isEmailConstraint;
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

        if ($currentPassword === '') {
            $errors['currentPassword'] = 'Current password is required';
        } elseif (!PasswordHasher::verify($currentPassword, $user->passwordHash)) {
            $errors['currentPassword'] = 'Current password is incorrect';
        }

        $newPasswordError = UserValidationHelper::checkPasswordLength($newPassword);
        if ($newPasswordError !== null) {
            $errors['newPassword'] = $newPasswordError;
        }

        $confirmPasswordError = UserValidationHelper::checkPasswordConfirmation(password: $newPassword, confirmPassword: $confirmPassword);
        if ($confirmPasswordError !== null) {
            $errors['confirmPassword'] = $confirmPasswordError;
        }

        if (!isset($errors['newPassword']) && PasswordHasher::verify($newPassword, $user->passwordHash)) {
            $errors['newPassword'] = 'New password must be different from current password.';
        }

        return $errors;
    }

    private function sendPasswordUpdateConfirmationEmail(UserAccount $user, int $userId): void
    {
        try {
            $userName = trim($user->firstName . ' ' . $user->lastName);
            $this->emailService->sendAccountUpdateConfirmationEmail($user->email, $userName, 'password',);
        } catch (EmailDeliveryException | SmtpNotConfiguredException $error) {
            $this->logEmailDeliveryFailure('password update confirmation', $userId, $error,);
        } catch (\Throwable $error) {
            $this->logUnexpectedEmailFailure('password update confirmation', $userId, $error,);
        }
    }

}


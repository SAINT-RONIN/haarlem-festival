<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\UserRoleId;
use App\Helpers\UserValidationHelper;
use App\Models\UserAccount;
use App\DTOs\Domain\User\UserWithRole;
use App\Exceptions\CmsOperationException;
use App\Repositories\Interfaces\ICmsUsersRepository;
use App\Repositories\Interfaces\IUserAccountRepository;
use App\Services\Interfaces\ICmsUsersService;
use App\Utils\PasswordHasher;

class CmsUsersService implements ICmsUsersService
{
    public function __construct(
        private readonly ICmsUsersRepository $usersRepository,
        private readonly IUserAccountRepository $userAccountRepository,
    ) {}

    /** @return UserWithRole[] */
    public function getUsersWithRoles(
        ?int $roleFilter = null,
        ?string $search = null,
        string $sortBy = 'registered',
        string $sortDir = 'desc',
    ): array {
        return $this->usersRepository->findUsersWithRoles($roleFilter, $search, $sortBy, $sortDir);
    }

    public function findById(int $id): ?UserAccount
    {
        return $this->usersRepository->findById($id);
    }

    /** @return array<string, string> */
    public function validateForCreate(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): array {
        $errors = $this->checkUsername($username, []);
        $errors = $this->checkEmail($email, $errors);
        $errors = $this->validatePasswordRequired($password, $errors);
        $errors = $this->validateNames($firstName, $lastName, $errors);
        return $this->validateRoleId($roleId, $errors);
    }

    // $id excludes current user from uniqueness checks so they can keep their own values.
    /** @return array<string, string> */
    public function validateForUpdate(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): array {
        $errors = $this->checkUsername($username, [], $id);
        $errors = $this->checkEmail($email, $errors, $id);
        $errors = $this->validatePasswordOptional($password, $errors);
        $errors = $this->validateNames($firstName, $lastName, $errors);
        return $this->validateRoleId($roleId, $errors);
    }

    /** @throws CmsOperationException */
    public function createUser(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int {
        try {
            $hash = PasswordHasher::hash($password);
            return $this->userAccountRepository->createUser($username, $email, $hash, $firstName, $lastName, $roleId);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create user.', 0, $error);
        }
    }

    // Password only updated when non-empty; blank keeps the existing password.
    /** @throws CmsOperationException */
    public function updateUser(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void {
        try {
            $this->userAccountRepository->updateUser($id, $username, $email, $firstName, $lastName, $roleId);

            // null means the field was not submitted; '' means it was submitted empty. Both mean "leave it alone".
            if ($password !== null && $password !== '') {
                $this->userAccountRepository->updatePasswordHash($id, PasswordHasher::hash($password));
            }
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update user.', 0, $error);
        }
    }

    // Hard delete — repository must clean up related rows.
    /** @throws CmsOperationException */
    public function deleteUser(int $id): void
    {
        try {
            $this->userAccountRepository->deleteUser($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete user.', 0, $error);
        }
    }

    /** @throws CmsOperationException */
    public function reactivateUser(int $id): void
    {
        try {
            $this->userAccountRepository->reactivateUser($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to reactivate user.', 0, $error);
        }
    }

    // Format checked before uniqueness; $excludeId lets the user keep their own value on update.
    private function checkUsername(string $username, array $errors, ?int $excludeId = null): array
    {
        $fmtError = UserValidationHelper::checkUsernameFormat($username);
        if ($fmtError !== null) {
            $errors['username'] = $fmtError;
            return $errors;
        }
        // On update, exclude the current user from uniqueness checks so they can keep their own username
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByUsernameExcluding($username, $excludeId)
            : $this->usersRepository->existsByUsername($username);
        if ($taken) {
            $errors['username'] = 'This username is already taken.';
        }
        return $errors;
    }

    private function checkEmail(string $email, array $errors, ?int $excludeId = null): array
    {
        $formatError = UserValidationHelper::checkEmail($email);
        if ($formatError !== null) {
            $errors['email'] = $formatError;
            return $errors;
        }
        // On update, exclude the current user so they can keep their own email
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByEmailExcluding($email, $excludeId)
            : $this->usersRepository->existsByEmail($email);
        if ($taken) {
            $errors['email'] = 'This email is already registered.';
        }
        return $errors;
    }

    private function validatePasswordRequired(string $password, array $errors): array
    {
        $lengthError = UserValidationHelper::checkPasswordLength($password);
        if ($lengthError !== null) {
            $errors['password'] = $lengthError;
        }

        return $errors;
    }

    // Empty value is silently accepted — on update the user may leave the field blank.
    private function validatePasswordOptional(?string $password, array $errors): array
    {
        if ($password !== null && $password !== '') {
            $lengthError = UserValidationHelper::checkPasswordLength($password);
            if ($lengthError !== null) {
                $errors['password'] = $lengthError;
            }
        }

        return $errors;
    }

    private function validateNames(string $firstName, string $lastName, array $errors): array
    {
        return array_merge($errors, UserValidationHelper::checkNames($firstName, $lastName));
    }

    // Prevents arbitrary integers from reaching the DB role column.
    private function validateRoleId(int $roleId, array $errors): array
    {
        if (UserRoleId::tryFrom($roleId) === null) {
            $errors['roleId'] = 'Please select a valid role.';
        }
        return $errors;
    }
}

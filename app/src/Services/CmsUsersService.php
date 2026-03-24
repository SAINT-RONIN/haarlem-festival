<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\UserValidationHelper;
use App\Models\UserAccount;
use App\Models\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;
use App\Services\Interfaces\ICmsUsersService;
use App\Utils\PasswordHasher;

/**
 * CMS-side user management: listing, creating, updating, and deleting user accounts.
 *
 * Applies field-level validation (username/email uniqueness, password strength, name format)
 * before delegating persistence to the repository. Password hashing uses Argon2id.
 */
class CmsUsersService implements ICmsUsersService
{

    public function __construct(
        private readonly ICmsUsersRepository $usersRepository,
    ) {
    }

    /**
     * Returns all user accounts joined with their role names, with optional filtering and sorting.
     *
     * @return UserWithRole[]
     */
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

    /**
     * Validates all fields required when creating a new user (password is mandatory).
     *
     * @return array<string, string> Field name => error message (empty if valid)
     */
    public function validateForCreate(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
    ): array {
        $errors = $this->checkUsername($username, []);
        $errors = $this->checkEmail($email, $errors);
        $errors = $this->validatePasswordRequired($password, $errors);
        return $this->validateNames($firstName, $lastName, $errors);
    }

    /**
     * Validates fields for an existing user update. Password is optional (only validated if provided).
     * Uniqueness checks exclude the user being edited.
     *
     * @return array<string, string> Field name => error message (empty if valid)
     */
    public function validateForUpdate(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
    ): array {
        $errors = $this->checkUsername($username, [], $id);
        $errors = $this->checkEmail($email, $errors, $id);
        $errors = $this->validatePasswordOptional($password, $errors);
        return $this->validateNames($firstName, $lastName, $errors);
    }

    /**
     * Creates a new user account with a hashed password and the specified role.
     *
     * @return int The newly created user's ID
     */
    public function createUser(
        string $username,
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): int {
        $hash = PasswordHasher::hash($password);

        return $this->usersRepository->createUser($username, $email, $hash, $firstName, $lastName, $roleId);
    }

    /**
     * Updates a user's profile fields and role. Password is only re-hashed if a new one is provided.
     */
    public function updateUser(
        int $id,
        string $username,
        string $email,
        ?string $password,
        string $firstName,
        string $lastName,
        int $roleId,
    ): void {
        $this->usersRepository->updateUser($id, $username, $email, $firstName, $lastName, $roleId);

        if ($password !== null && $password !== '') {
            $this->usersRepository->updateUserPassword($id, PasswordHasher::hash($password));
        }
    }

    public function deleteUser(int $id): void
    {
        $this->usersRepository->deleteUser($id);
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function checkUsername(string $username, array $errors, ?int $excludeId = null): array
    {
        $fmtError = UserValidationHelper::checkUsernameFormat($username);
        if ($fmtError !== null) {
            $errors['username'] = $fmtError;
            return $errors;
        }
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByUsernameExcluding($username, $excludeId)
            : $this->usersRepository->existsByUsername($username);
        if ($taken) {
            $errors['username'] = 'This username is already taken.';
        }
        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function checkEmail(string $email, array $errors, ?int $excludeId = null): array
    {
        $formatError = UserValidationHelper::checkEmail($email);
        if ($formatError !== null) {
            $errors['email'] = $formatError;
            return $errors;
        }
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByEmailExcluding($email, $excludeId)
            : $this->usersRepository->existsByEmail($email);
        if ($taken) {
            $errors['email'] = 'This email is already registered.';
        }
        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validatePasswordRequired(string $password, array $errors): array
    {
        $lengthError = UserValidationHelper::checkPasswordLength($password);
        if ($lengthError !== null) {
            $errors['password'] = $lengthError;
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
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

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validateNames(string $firstName, string $lastName, array $errors): array
    {
        return array_merge($errors, UserValidationHelper::checkNames($firstName, $lastName));
    }
}

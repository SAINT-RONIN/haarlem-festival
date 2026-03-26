<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\UserAccount;
use App\Models\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;
use App\Services\Interfaces\ICmsUsersService;
use App\Utils\PasswordHasher;

class CmsUsersService implements ICmsUsersService
{
    private const USERNAME_MIN_LENGTH = 3;
    private const USERNAME_MAX_LENGTH = 60;
    private const PASSWORD_MIN_LENGTH = 8;

    public function __construct(
        private readonly ICmsUsersRepository $usersRepository,
    ) {
    }

    /**
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
     * @return array<string, string>
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
     * @return array<string, string>
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
        $fmtError = $this->checkUsernameFormat($username);
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
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
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

    private function checkUsernameFormat(string $username): ?string
    {
        if ($username === '') {
            return 'Username is required.';
        }
        if (strlen($username) < self::USERNAME_MIN_LENGTH) {
            return 'Username must be at least ' . self::USERNAME_MIN_LENGTH . ' characters.';
        }
        if (strlen($username) > self::USERNAME_MAX_LENGTH) {
            return 'Username must be no more than ' . self::USERNAME_MAX_LENGTH . ' characters.';
        }
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            return 'Username can only contain letters, numbers, underscores, and hyphens.';
        }
        return null;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validatePasswordRequired(string $password, array $errors): array
    {
        if ($password === '') {
            $errors['password'] = 'Password is required.';
            return $errors;
        }
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters.';
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validatePasswordOptional(?string $password, array $errors): array
    {
        if ($password !== null && $password !== '' && strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters.';
        }

        return $errors;
    }

    /**
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validateNames(string $firstName, string $lastName, array $errors): array
    {
        if (trim($firstName) === '') {
            $errors['firstName'] = 'First name is required.';
        }
        if (trim($lastName) === '') {
            $errors['lastName'] = 'Last name is required.';
        }

        return $errors;
    }
}

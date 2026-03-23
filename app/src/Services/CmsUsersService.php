<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\UserValidationHelper;
use App\Models\UserAccount;
use App\Models\UserWithRole;
use App\Repositories\Interfaces\ICmsUsersRepository;
use App\Services\Interfaces\ICmsUsersService;
use App\Utils\PasswordHasher;

class CmsUsersService implements ICmsUsersService
{

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

    private function checkUsernameFormat(string $username): ?string
    {
        return UserValidationHelper::checkUsernameFormat($username);
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

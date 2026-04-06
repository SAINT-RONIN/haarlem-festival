<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\UserValidationHelper;
use App\Models\UserAccount;
use App\DTOs\Domain\User\UserWithRole;
use App\Exceptions\CmsOperationException;
use App\Repositories\Interfaces\ICmsUsersRepository;
use App\Repositories\Interfaces\IUserAccountRepository;
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
        private readonly IUserAccountRepository $userAccountRepository,
    ) {
    }

    /**
     * Returns all user accounts joined with their role names, with optional filtering and sorting.
     *
     * $roleFilter narrows the list to one role when set; null returns all roles.
     * $search filters by name or email (partial match). $sortBy and $sortDir control ordering.
     * The defaults (sorted by registered date, newest first) match the expected CMS list order.
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

    /**
     * Loads a single user account by its id, used by the CMS edit page before rendering the form.
     *
     * Returns null when no user exists with that id — the caller must null-check before using the result.
     */
    public function findById(int $id): ?UserAccount
    {
        return $this->usersRepository->findById($id);
    }

    /**
     * Validates all fields required when creating a new user.
     *
     * Errors are collected into a single array (field name => message) rather than stopping
     * at the first problem, so the form can show all issues at once. A password is required
     * here because an account cannot exist without one (unlike update, where it is optional).
     *
     * @return array<string, string> Field name => error message, empty if everything is valid
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
     * Validates all fields when editing an existing user account.
     *
     * $id is passed to the username and email checks so the user can keep their own
     * username or email without triggering a "already taken" error — uniqueness is
     * checked against everyone except the user being edited.
     * Password is optional: leaving it blank keeps the existing password unchanged.
     *
     * @return array<string, string> Field name => error message, empty if everything is valid
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
     * Creates a new user account and returns the new user's id.
     *
     * The password is hashed before it reaches the repository because the repository
     * only ever stores hashes — it never receives a plaintext password.
     *
     * @return int The newly created user's id
     * @throws CmsOperationException When the database write fails
     */
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

    /**
     * Updates a user's profile fields and role assignment.
     *
     * The password is only updated when a non-empty value is provided. Submitting the
     * edit form without touching the password field leaves the existing password untouched.
     *
     * @throws CmsOperationException When the database write fails
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

    /**
     * Permanently deletes a user account.
     *
     * This is a hard delete with no soft-delete safety net. The repository is responsible
     * for cleaning up any related rows (sessions, role links, etc.).
     *
     * @throws CmsOperationException When the database write fails
     */
    public function deleteUser(int $id): void
    {
        try {
            $this->userAccountRepository->deleteUser($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete user.', 0, $error);
        }
    }

    /**
     * Reactivates a previously deactivated user account.
     *
     * @throws CmsOperationException When the database write fails
     */
    public function reactivateUser(int $id): void
    {
        try {
            $this->userAccountRepository->reactivateUser($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to reactivate user.', 0, $error);
        }
    }

    /**
     * Checks username format first, then uniqueness against existing accounts.
     *
     * Format is checked before uniqueness because there is no point querying the DB
     * for a username that is already invalid. When $excludeId is set the uniqueness
     * check skips that user, so they can keep their own username on update.
     *
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
        // On update, exclude the current user from uniqueness checks so they can keep their own username
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByUsernameExcluding($username, $excludeId)
            : $this->usersRepository->existsByUsername($username);
        if ($taken) {
            $errors['username'] = 'This username is already taken.';
        }
        return $errors;
    }

    /**
     * Checks email format first, then uniqueness against existing accounts.
     *
     * Same pattern as checkUsername: format check before DB query, and $excludeId
     * allows the current user to keep their own email on update.
     *
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
        // On update, exclude the current user so they can keep their own email
        $taken = $excludeId !== null
            ? $this->usersRepository->existsByEmailExcluding($email, $excludeId)
            : $this->usersRepository->existsByEmail($email);
        if ($taken) {
            $errors['email'] = 'This email is already registered.';
        }
        return $errors;
    }

    /**
     * Validates that a password meets the minimum length rule.
     *
     * Used on create where an empty password is a failure — an account cannot be
     * created without one.
     *
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
     * Validates a password only when one is actually provided.
     *
     * Unlike validatePasswordRequired, a null or empty value is silently accepted here
     * because on update the user may leave the field blank to keep their existing password.
     * If a new password is provided it must still meet the minimum length rule.
     *
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
     * Validates first and last name via the shared UserValidationHelper and merges
     * any errors into the existing error array rather than replacing it.
     *
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function validateNames(string $firstName, string $lastName, array $errors): array
    {
        return array_merge($errors, UserValidationHelper::checkNames($firstName, $lastName));
    }
}

<?php

declare(strict_types=1);

namespace App\Helpers;

//Shared validation rules for user accounts.
//Used by both AuthService (registration) and CmsUsersService (admin user management) to ensure consistent username, email, password, and name validation.
final class UserValidationHelper
{
    public const USERNAME_MIN_LENGTH = 3;
    public const USERNAME_MAX_LENGTH = 60;
    public const PASSWORD_MIN_LENGTH = 8;
    public const NAME_MIN_LENGTH = 2;
    public const NAME_MAX_LENGTH = 100;


    public static function checkUsernameFormat(string $username): ?string
    {
        if ($username === '' || empty(trim($username))) {
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

    //Does NOT check uniqueness (that requires a repository).
    public static function checkEmail(string $email): ?string
    {
        if (empty($email)) {
            return 'Email is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter a valid email address.';
        }

        return null;
    }

    public static function checkPasswordLength(string $password): ?string
    {
        if ($password === '') {
            return 'Password is required.';
        }
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            return 'Password must be at least ' . self::PASSWORD_MIN_LENGTH . ' characters.';
        }

        return null;
    }

    public static function checkPasswordConfirmation(string $password, string $confirmPassword): ?string {
        if ($confirmPassword === '') {
            return 'Please confirm your password.';
        }
        if ($password !== $confirmPassword) {
            return 'Passwords do not match.';
        }

        return null;
    }

    public static function checkNameField(string $name, string $shownField): ?string{
        $name = trim($name);

        if ($name === '') {
            return $shownField . ' is required.';
        }

        if (mb_strlen($name) < self::NAME_MIN_LENGTH) {
            return $shownField . ' must be at least ' . self::NAME_MIN_LENGTH . ' characters.';
        }

        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            return $shownField . ' must not exceed ' . self::NAME_MAX_LENGTH . ' characters.';
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    public static function checkNames(string $firstName, string $lastName): array
    {
        $errors = [];
        if (trim($firstName) === '') {
            $errors['firstName'] = 'First name is required.';
        }
        if (trim($lastName) === '') {
            $errors['lastName'] = 'Last name is required.';
        }

        return $errors;
    }
}

<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Auth\RegistrationFormData;

/**
 * Maps raw registration input into the typed registration DTO used by AuthService.
 */
final class AuthMapper
{
    /**
     * @param array<string, mixed> $input
     */
    public static function fromRegistrationInput(array $input): RegistrationFormData
    {
        return new RegistrationFormData(
            username: (string)($input['username'] ?? ''),
            email: (string)($input['email'] ?? ''),
            password: (string)($input['password'] ?? ''),
            confirmPassword: (string)($input['confirm_password'] ?? ''),
            firstName: (string)($input['first_name'] ?? ''),
            lastName: (string)($input['last_name'] ?? ''),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Auth;

/**
 * Typed carrier for user registration form fields.
 * Built from raw request data by AuthMapper, then validated by AuthService.
 */
final readonly class RegistrationFormData
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $confirmPassword,
        public string $firstName,
        public string $lastName,
    ) {}

    /**
     * Converts to an array for flash-based form repopulation after validation errors.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'confirmPassword' => $this->confirmPassword,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed representation of user registration form data from $_POST.
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
    ) {
    }

    /**
     * Converts to the associative array format expected by AuthService.
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

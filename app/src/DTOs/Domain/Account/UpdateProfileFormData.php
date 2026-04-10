<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Account;

/**
 * Form data for profile updates (email, name, optional password change).
 * Used by AccountController to validate and pass data to the account service.
 */
final readonly class UpdateProfileFormData
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public ?string $newPassword = null,
        public ?string $confirmPassword = null,
        public ?int $profilePictureAssetId = null,
    ) {}

    /**
     * Converts form input to this DTO.
     *
     * @param array<string, mixed> $input
     */
    public static function fromInput(array $input): self
    {
        return new self(
            email: trim($input['email'] ?? ''),
            firstName: trim($input['firstName'] ?? ''),
            lastName: trim($input['lastName'] ?? ''),
            newPassword: !empty($input['newPassword']) ? $input['newPassword'] : null,
            confirmPassword: !empty($input['confirmPassword']) ? $input['confirmPassword'] : null,
            profilePictureAssetId: !empty($input['profilePictureAssetId']) ? (int) $input['profilePictureAssetId'] : null,
        );
    }

    /**
     * Converts to array for flash storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'profilePictureAssetId' => $this->profilePictureAssetId,
        ];
    }
}


<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Account;

//Form data for profile updates (email, name, profile photo change)
final readonly class UpdateProfileFormData
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public ?int $profilePictureAssetId = null,
    ) {}

    /**
     * @param array<string, mixed> $input
     */
    public static function fromInput(array $input): self
    {
        return new self(
            email: trim($input['email'] ?? ''),
            firstName: trim($input['firstName'] ?? ''),
            lastName: trim($input['lastName'] ?? ''),
            profilePictureAssetId: !empty($input['profilePictureAssetId']) ? (int) $input['profilePictureAssetId'] : null,
        );
    }

    /**
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

    public function withProfilePictureAssetId(?int $profilePictureAssetId): self
    {
        return new self(
            email: $this->email,
            firstName: $this->firstName,
            lastName: $this->lastName,
            profilePictureAssetId: $profilePictureAssetId,
        );
    }
}


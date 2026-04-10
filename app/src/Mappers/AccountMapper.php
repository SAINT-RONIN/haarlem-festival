<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Account\UpdateProfileFormData;

/**
 * Maps raw form input into typed account DTOs used by AccountService.
 */
final class AccountMapper
{
    /**
     * Maps profile update form input to UpdateProfileFormData DTO.
     *
     * @param array<string, mixed> $input
     */
    public static function fromProfileUpdateInput(array $input): UpdateProfileFormData
    {
        return UpdateProfileFormData::fromInput($input);
    }
}


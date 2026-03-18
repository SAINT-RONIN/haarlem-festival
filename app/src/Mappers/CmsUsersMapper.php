<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\UserWithRole;
use App\ViewModels\Cms\CmsUserListItemViewModel;
use App\ViewModels\Cms\CmsUsersListViewModel;

class CmsUsersMapper
{
    public static function toListViewModel(
        array $users,
        string $selectedRole,
        ?string $successMessage,
        ?string $errorMessage
    ): CmsUsersListViewModel {
        return new CmsUsersListViewModel(
            users: array_map([self::class, 'toListItem'], $users),
            selectedRole: $selectedRole,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }


    public static function toListItem(UserWithRole $user): CmsUserListItemViewModel
    {
        $roleName = $user->roleName ?? 'Unknown';
        $isActive = $user->isActive;

        return new CmsUserListItemViewModel(
            userAccountId:    $user->userAccountId,
            username:         $user->username,
            email:            $user->email,
            fullName:         trim($user->firstName . ' ' . $user->lastName),
            roleName:         $roleName,
            roleBadgeClass:   self::resolveRoleBadgeClass($roleName),
            isActive:         $isActive,
            statusText:       $isActive ? 'Active' : 'Inactive',
            statusBadgeClass: $isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
            registeredAt:     $user->registeredAtUtc !== ''
                                  ? (new \DateTimeImmutable($user->registeredAtUtc))->format('d M Y, H:i')
                                  : '',
        );
    }

    private static function resolveRoleBadgeClass(string $roleName): string
    {
        return match ($roleName) {
            'Administrator' => 'bg-purple-100 text-purple-800',
            'Employee'      => 'bg-blue-100 text-blue-800',
            'Customer'      => 'bg-gray-100 text-gray-800',
            default         => 'bg-gray-100 text-gray-800',
        };
    }
}

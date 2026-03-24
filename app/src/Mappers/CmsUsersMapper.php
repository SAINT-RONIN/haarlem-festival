<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\UserRoleId;
use App\Helpers\FormatHelper;
use App\Models\UserAccount;
use App\Models\UserWithRole;
use App\ViewModels\Cms\CmsSortColumnViewModel;
use App\ViewModels\Cms\CmsUserFormViewModel;
use App\ViewModels\Cms\CmsUserListItemViewModel;
use App\ViewModels\Cms\CmsUsersListViewModel;

/**
 * Transforms user-account domain models into ViewModels for the CMS user-management pages
 * (user list with sorting/filtering, and user create/edit form).
 */
final class CmsUsersMapper
{
    /**
     * Builds the CMS users-list page ViewModel, including sort column links,
     * role-filter options, and role/status badge classes for each user row.
     *
     * @param UserWithRole[] $users
     */
    public static function toListViewModel(
        array $users,
        string $selectedRole,
        ?string $successMessage,
        ?string $errorMessage,
        string $searchQuery = '',
        string $sortBy = 'registered',
        string $sortDir = 'desc',
        string $deleteCsrfToken = '',
    ): CmsUsersListViewModel {
        return new CmsUsersListViewModel(
            users:             array_map([self::class, 'toListItem'], $users),
            selectedRole:      $selectedRole,
            successMessage:    $successMessage,
            errorMessage:      $errorMessage,
            searchQuery:       $searchQuery,
            sortBy:            $sortBy,
            sortDir:           $sortDir,
            deleteCsrfToken:   $deleteCsrfToken,
            roleFilterOptions: self::buildRoleOptions(),
            sortColumns:       self::buildSortColumns($sortBy, $sortDir, $selectedRole, $searchQuery),
            hasActiveFilters:  $selectedRole !== '' || $searchQuery !== '',
        );
    }

    /**
     * Converts a single UserWithRole into a list-row ViewModel, resolving the
     * role badge color and the active/inactive status badge for the CMS table.
     */
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
                                  ? (new \DateTimeImmutable($user->registeredAtUtc))->format(FormatHelper::CMS_DATE_FORMAT)
                                  : '',
        );
    }

    /**
     * Builds the CMS user create/edit form ViewModel with role options and validation errors.
     * Serves both the "new user" and "edit user" pages (distinguished by $user being null or not).
     *
     * @param array<string, string> $errors
     */
    public static function toFormViewModel(
        ?UserAccount $user,
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        int $selectedRoleId,
        string $csrfToken,
        string $formAction,
        string $pageTitle,
        array $errors,
    ): CmsUserFormViewModel {
        return new CmsUserFormViewModel(
            userAccountId:  $user?->userAccountId,
            username:       $username,
            email:          $email,
            firstName:      $firstName,
            lastName:       $lastName,
            selectedRoleId: $selectedRoleId,
            csrfToken:      $csrfToken,
            formAction:     $formAction,
            pageTitle:      $pageTitle,
            errors:         $errors,
            roleOptions:    self::buildRoleOptions(),
        );
    }

    /**
     * @return array<string, CmsSortColumnViewModel>
     */
    /**
     * Generates sortable column header ViewModels — each contains the toggled URL
     * and the appropriate chevron icon reflecting the current sort state.
     */
    private static function buildSortColumns(string $sortBy, string $sortDir, string $selectedRole, string $searchQuery): array
    {
        $columns = [];
        foreach (['username', 'email', 'name', 'role', 'registered'] as $col) {
            $dir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
            $params = array_filter(['sort' => $col, 'dir' => $dir, 'role' => $selectedRole, 'search' => $searchQuery]);
            $icon = $sortBy !== $col ? 'chevrons-up-down' : ($sortDir === 'asc' ? 'chevron-up' : 'chevron-down');
            $columns[$col] = new CmsSortColumnViewModel(url: '/cms/users?' . http_build_query($params), icon: $icon);
        }
        return $columns;
    }

    /** Maps a role name to its Tailwind badge color classes for the CMS user list. */
    private static function resolveRoleBadgeClass(string $roleName): string
    {
        return match ($roleName) {
            'Administrator' => 'bg-purple-100 text-purple-800',
            'Employee'      => 'bg-blue-100 text-blue-800',
            'Customer'      => 'bg-gray-100 text-gray-800',
            default         => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * @return array<int, string>
     */
    private static function buildRoleOptions(): array
    {
        $options = [];
        foreach (UserRoleId::cases() as $role) {
            $options[$role->value] = $role->getDisplayName();
        }
        return $options;
    }
}

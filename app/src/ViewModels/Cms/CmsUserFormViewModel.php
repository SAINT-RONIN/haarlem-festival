<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS user create/edit form.
 * userAccountId is null for create, set for edit.
 */
final readonly class CmsUserFormViewModel
{
    /**
     * @param array<string, string> $errors
     * @param array<int, string>    $roleOptions
     */
    public function __construct(
        public ?int    $userAccountId,
        public string  $username,
        public string  $email,
        public string  $firstName,
        public string  $lastName,
        public int     $selectedRoleId,
        public string  $csrfToken,
        public string  $formAction,
        public string  $pageTitle,
        public array   $errors,
        public array   $roleOptions,
    ) {}
}

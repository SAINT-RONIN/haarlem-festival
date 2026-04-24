<?php

declare(strict_types=1);

namespace App\ViewModels\Account;

use App\Models\UserAccount;

/**
 * ViewModel for the account edit form partial.
 * Contains all pre-formatted data needed by the account edit form view.
 * The controller prepares this data so the view only needs to render.
 */
final readonly class AccountFormViewModel
{
    /**
     * @param array<string, string> $errors
     * @param array<string, mixed> $oldInput
     */
    public function __construct(
        public UserAccount $user,
        public array $errors,
        public string $successMessage,
        public array $oldInput,
    ) {
    }
}


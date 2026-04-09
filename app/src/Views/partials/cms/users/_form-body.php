<?php
/**
 * User form body — the inner content of the CMS user create/edit form.
 * Rendered inside the CMS shell via CmsPageLayout.
 *
 * @var \App\ViewModels\Cms\CmsUserFormViewModel $viewModel
 */

$isEditMode = $viewModel->userAccountId !== null;
$subtitleText = $isEditMode
    ? 'Update the details for this user account.'
    : 'Fill in the details to create a new user account.';
?>
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($viewModel->pageTitle) ?></h1>
                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($subtitleText) ?></p>
                </div>
                <a href="/cms/users" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-800">
                    <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                    Back to Users
                </a>
            </div>
        </header>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">User Details</h2>
            </div>
            <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">

                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- First Name -->
                    <div>
                        <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="firstName"
                               id="firstName"
                               value="<?= htmlspecialchars($viewModel->firstName) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['firstName']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['firstName'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['firstName']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="lastName"
                               id="lastName"
                               value="<?= htmlspecialchars($viewModel->lastName) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['lastName']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['lastName'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['lastName']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="username"
                               id="username"
                               value="<?= htmlspecialchars($viewModel->username) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['username']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['username'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['username']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="<?= htmlspecialchars($viewModel->email) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['email']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['email'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div>
                        <?php if ($isEditMode): ?>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                New Password (leave blank to keep current)
                            </label>
                        <?php else: ?>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span class="text-red-500">*</span>
                            </label>
                        <?php endif; ?>
                        <input type="password"
                               name="password"
                               id="password"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['password']) ? 'border-red-500' : '' ?>">
                        <?php if ($isEditMode): ?>
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password.</p>
                        <?php endif; ?>
                        <?php if (isset($viewModel->errors['password'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="roleId" class="block text-sm font-medium text-gray-700 mb-1">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="roleId"
                                id="roleId"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['roleId']) ? 'border-red-500' : '' ?>">
                            <?php foreach ($viewModel->roleOptions as $roleId => $roleName): ?>
                                <option value="<?= htmlspecialchars((string) $roleId) ?>"
                                    <?= $viewModel->selectedRoleId === $roleId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roleName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($viewModel->errors['roleId'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['roleId']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 flex justify-end gap-3">
                    <a href="/cms/users" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</a>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                        Save User
                    </button>
                </div>
            </form>
        </div>

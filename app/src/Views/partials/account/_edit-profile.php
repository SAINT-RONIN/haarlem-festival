<?php
/**
 * Account profile edit form partial.
 *
 * @var \App\ViewModels\Account\AccountFormViewModel $viewModel
 */
?>

<section class="w-full px-4 sm:px-6 md:px-8 lg:px-12 xl:px-24 py-8 sm:py-10 md:py-12 lg:py-16">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-slate-800 text-3xl sm:text-4xl md:text-5xl font-bold mb-2">Account Settings</h1>
            <p class="text-slate-600 text-base sm:text-lg">Manage your profile and account information</p>
        </div>

        <!-- Success Message -->
        <?php if ($viewModel->successMessage): ?>
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 rounded">
                <p class="text-green-700 font-semibold"><?= htmlspecialchars($viewModel->successMessage) ?></p>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($viewModel->errors['error'])): ?>
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red rounded">
                <p class="text-red font-semibold"><?= htmlspecialchars($viewModel->errors['error']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6">Profile Information</h2>

            <form method="POST" action="/account/update-profile" enctype="multipart/form-data" class="space-y-5">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?= htmlspecialchars($viewModel->oldInput['email'] ?? $viewModel->user->email) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['email']) ? 'border-red bg-red-50' : '' ?>">
                    <?php if (isset($viewModel->errors['email'])): ?>
                        <p class="text-red text-sm mt-1"><?= htmlspecialchars($viewModel->errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- First Name -->
                <div>
                    <label for="firstName" class="block text-sm font-semibold text-slate-700 mb-2">First Name</label>
                    <input type="text"
                           id="firstName"
                           name="firstName"
                           value="<?= htmlspecialchars($viewModel->oldInput['firstName'] ?? $viewModel->user->firstName) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['firstName']) ? 'border-red bg-red-50' : '' ?>">
                    <?php if (isset($viewModel->errors['firstName'])): ?>
                        <p class="text-red text-sm mt-1"><?= htmlspecialchars($viewModel->errors['firstName']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Last Name -->
                <div>
                    <label for="lastName" class="block text-sm font-semibold text-slate-700 mb-2">Last Name</label>
                    <input type="text"
                           id="lastName"
                           name="lastName"
                           value="<?= htmlspecialchars($viewModel->oldInput['lastName'] ?? $viewModel->user->lastName) ?>"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['lastName']) ? 'border-red bg-red-50' : '' ?>">
                    <?php if (isset($viewModel->errors['lastName'])): ?>
                        <p class="text-red text-sm mt-1"><?= htmlspecialchars($viewModel->errors['lastName']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Profile Picture (Optional) -->
                <div>
                    <label for="profilePicture" class="block text-sm font-semibold text-slate-700 mb-2">Profile Picture (Optional)</label>
                    <input type="file"
                           id="profilePicture"
                           name="profilePicture"
                           accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent">
                    <p class="text-slate-500 text-xs mt-1">JPG, PNG - max 5MB</p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-red hover:bg-royal-blue text-white font-semibold rounded-lg transition-colors duration-200">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Change Card -->
        <div class="bg-white rounded-lg shadow-md p-6 sm:p-8" id="change-password-section">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6">Change Password</h2>

            <form method="POST" action="/account/update-password" class="space-y-5">
                <!-- Current Password -->
                <div>
                    <label for="currentPassword" class="block text-sm font-semibold text-slate-700 mb-2">Current Password</label>
                    <input type="password"
                           id="currentPassword"
                           name="currentPassword"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['currentPassword']) ? 'border-red bg-red-50' : '' ?>">
                    <?php if (isset($viewModel->errors['currentPassword'])): ?>
                        <p class="text-red text-sm mt-2 font-medium"><?= htmlspecialchars($viewModel->errors['currentPassword']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- New Password -->
                <div>
                    <label for="newPassword" class="block text-sm font-semibold text-slate-700 mb-2">New Password</label>
                    <input type="password"
                           id="newPassword"
                           name="newPassword"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['newPassword']) || isset($viewModel->errors['confirmPassword']) ? 'border-red bg-red-50' : '' ?>">
                    <p class="text-slate-500 text-xs mt-1">Minimum 8 characters</p>
                    <?php if (isset($viewModel->errors['newPassword'])): ?>
                        <p class="text-red text-sm mt-2 font-medium"><?= htmlspecialchars($viewModel->errors['newPassword']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="confirmPassword" class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password</label>
                    <input type="password"
                           id="confirmPassword"
                           name="confirmPassword"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red focus:border-transparent <?= isset($viewModel->errors['confirmPassword']) ? 'border-red bg-red-50' : '' ?>">
                    <?php if (isset($viewModel->errors['confirmPassword'])): ?>
                        <p class="text-red text-sm mt-2 font-medium"><?= htmlspecialchars($viewModel->errors['confirmPassword']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-3 bg-red hover:bg-royal-blue text-white font-semibold rounded-lg transition-colors duration-200">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Message -->
        <div class="mt-8 p-4 bg-slate-50 rounded-lg border border-slate-200">
            <p class="text-sm text-slate-600">
                <strong>Note:</strong> Your profile information is stored securely.
                If you change your email address, you'll need to confirm it via a verification link sent to your new email.
            </p>
        </div>
    </div>
</section>

<?php
// Extract error detection from viewModel
$passwordErrors = [
    'newPassword' => isset($viewModel->errors['newPassword']),
    'confirmPassword' => isset($viewModel->errors['confirmPassword']),
    'currentPassword' => isset($viewModel->errors['currentPassword']),
    'password' => isset($viewModel->errors['password']),
];
$hasPasswordErrors = array_reduce($passwordErrors, fn($carry, $item) => $carry || $item, false);
?>

<?php if ($hasPasswordErrors): ?>
<!-- Load scroll-to-password-section script only when password errors exist -->
<script src="/assets/js/account-form-scroll.js"></script>
<?php endif; ?>



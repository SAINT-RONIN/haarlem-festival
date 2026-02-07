<?php
/**
 * Reset password page for website visitors.
 *
 * @var string $token The reset token from the URL
 * @var bool $validToken Whether the token is valid
 * @var string|null $error Error message to display
 */
$currentPage = 'reset-password';
$includeNav = true;
$token = $_GET['token'] ?? '';
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Reset Password</h1>
                <?php if ($validToken): ?>
                    <p class="text-gray-600">Enter your new password below</p>
                <?php endif; ?>
            </div>

            <?php if (!$validToken): ?>
                <!-- Invalid Token Message -->
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error ?? 'This password reset link is invalid or has expired.'); ?></p>
                </div>
                <div class="text-center">
                    <a href="/forgot-password" class="text-royal-blue hover:text-red font-medium transition-colors">
                        Request a new reset link
                    </a>
                </div>
            <?php else: ?>
                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Reset Password Form -->
                <form action="/reset-password" method="POST" class="space-y-6">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                                placeholder="Enter your new password (min. 8 characters)">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                required
                                autocomplete="new-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                                placeholder="Confirm your new password">
                    </div>

                    <!-- Submit Button -->
                    <button
                            type="submit"
                            class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200">
                        Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <!-- Back to Home -->
        <div class="mt-6 text-center">
            <a href="/" class="text-gray-600 hover:text-royal-blue transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>


<?php
/**
 * Forgot password page for website visitors.
 *
 * @var string|null $success Success message to display
 * @var string|null $error Error message to display
 */
$currentPage = 'forgot-password';
$includeNav = true;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Forgot Password</h1>
                <p class="text-gray-600">Enter your email to receive a password reset link</p>
            </div>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm"><?php echo htmlspecialchars($success); ?></p>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                </div>
            <?php endif; ?>

            <!-- Forgot Password Form -->
            <form action="/forgot-password" method="POST" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your email address">
                </div>

                <!-- Submit Button -->
                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200">
                    Send Reset Link
                </button>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-8 text-center">
                <a href="/login" class="text-royal-blue hover:text-red font-medium transition-colors">
                    ← Back to Login
                </a>
            </div>
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


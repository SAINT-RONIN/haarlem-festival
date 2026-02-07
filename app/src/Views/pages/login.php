<?php
/**
 * Login page for website visitors.
 *
 * @var string|null $error Error message to display
 * @var string|null $success Success message to display
 */
$currentPage = 'login';
$includeNav = true;
$useLayoutWrapper = true;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="flex-1 w-full bg-sand flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Welcome Back</h1>
                <p class="text-gray-600">Sign in to your Haarlem Festival account</p>
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

            <!-- Login Form -->
            <form action="/login" method="POST" class="space-y-6">
                <!-- Username or Email -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                        Username or Email
                    </label>
                    <input
                            type="text"
                            id="login"
                            name="login"
                            required
                            autocomplete="username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your username or email">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your password">
                </div>

                <!-- Forgot Password Link -->
                <div class="text-right">
                    <a href="/forgot-password" class="text-sm text-royal-blue hover:text-red transition-colors">
                        Forgot your password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200">
                    Sign In
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-8 text-center">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="/register" class="text-royal-blue hover:text-red font-medium transition-colors">
                        Register here
                    </a>
                </p>
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

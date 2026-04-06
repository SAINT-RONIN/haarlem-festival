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

<a href="#login-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<main id="login-main" class="flex-1 w-full bg-sand flex flex-col items-center justify-center px-4 py-12" tabindex="-1">
    <section class="w-full max-w-md" aria-labelledby="login-heading">
        <article class="bg-white rounded-2xl shadow-lg p-8">
            <header class="text-center mb-8">
                <h1 id="login-heading" class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Welcome
                    Back</h1>
                <p class="text-gray-600">Sign in to your Haarlem Festival account</p>
            </header>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <section class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" role="status"
                         aria-live="polite">
                    <p class="text-green-700 text-sm"><?php echo htmlspecialchars($success); ?></p>
                </section>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <section class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert" aria-live="assertive">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                </section>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="/login" method="POST">
                <fieldset class="space-y-6">
                    <legend class="sr-only">Login credentials</legend>

                    <!-- Username or Email -->
                    <section>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                            Username or Email
                        </label>
                        <input
                                type="text"
                                id="login"
                                name="login"
                                required
                                autocomplete="username"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                                placeholder="Enter your username or email">
                    </section>

                    <!-- Password -->
                    <section>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                                placeholder="Enter your password">
                    </section>

                    <!-- Forgot Password Link -->
                    <p class="text-right">
                        <a href="/forgot-password"
                           class="text-sm text-royal-blue hover:text-red transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                            Forgot your password?
                        </a>
                    </p>

                    <!-- Submit Button -->
                    <button
                            type="submit"
                            class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
                        Sign In
                    </button>
                </fieldset>
            </form>

            <!-- Register Link -->
            <p class="mt-8 text-center text-gray-600">
                Don't have an account?
                <a href="/register"
                   class="text-royal-blue hover:text-red font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                    Register here
                </a>
            </p>
        </article>

        <!-- Back to Home -->
        <nav class="mt-6 text-center" aria-label="Back to home">
            <a href="/"
               class="text-gray-600 hover:text-royal-blue transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                ← Back to Home
            </a>
        </nav>
    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

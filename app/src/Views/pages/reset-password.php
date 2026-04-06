<?php
/**
 * Reset password page for website visitors.
 *
 * @var string $token The reset token from the URL (passed from controller)
 * @var bool $validToken Whether the token is valid
 * @var string|null $error Error message to display
 */
$currentPage = 'reset-password';
$includeNav = true;
// $token is passed from the controller
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<a href="#reset-password-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<main id="reset-password-main" class="w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12"
      tabindex="-1">
    <section class="w-full max-w-md" aria-labelledby="reset-password-heading">
        <article class="bg-white rounded-2xl shadow-lg p-8">
            <header class="text-center mb-8">
                <h1 id="reset-password-heading" class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Reset
                    Password</h1>
                <?php if ($validToken): ?>
                    <p class="text-gray-600">Enter your new password below</p>
                <?php endif; ?>
            </header>

            <?php if (!$validToken): ?>
                <!-- Invalid Token Message -->
                <section class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert" aria-live="assertive">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error ?? 'This password reset link is invalid or has expired.'); ?></p>
                </section>
                <nav class="text-center" aria-label="Request new reset link">
                    <a href="/forgot-password"
                       class="text-royal-blue hover:text-red font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                        Request a new reset link
                    </a>
                </nav>
            <?php else: ?>
                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                    <section class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert"
                             aria-live="assertive">
                        <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
                    </section>
                <?php endif; ?>

                <!-- Reset Password Form -->
                <form action="/reset-password" method="POST">
                    <fieldset class="space-y-6">
                        <legend class="sr-only">Reset password details</legend>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <!-- New Password -->
                        <section>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New
                                Password</label>
                            <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                                    placeholder="Enter your new password (min. 8 characters)">
                        </section>

                        <!-- Confirm Password -->
                        <section>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                                New Password</label>
                            <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    required
                                    autocomplete="new-password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                                    placeholder="Confirm your new password">
                        </section>

                        <!-- Submit Button -->
                        <button
                                type="submit"
                                class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
                            Reset Password
                        </button>
                    </fieldset>
                </form>
            <?php endif; ?>
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


<?php
/**
 * Forgot-password form body — rendered inside the shared shell's <main>.
 *
 * @var string|null $success
 * @var string|null $error
 */
?>
<a href="#forgot-password-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<section class="w-full max-w-md" aria-labelledby="forgot-password-heading">
    <article class="bg-white rounded-2xl shadow-lg p-8">
        <header class="text-center mb-8">
            <h1 id="forgot-password-heading" class="text-3xl font-bold text-royal-blue font-serif-display mb-2">
                Forgot Password</h1>
            <p class="text-gray-600">Enter your email to receive a password reset link</p>
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

        <!-- Forgot Password Form -->
        <form action="/forgot-password" method="POST">
            <fieldset class="space-y-6">
                <legend class="sr-only">Password reset request</legend>

                <!-- Email -->
                <section>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Enter your email address">
                </section>

                <!-- Submit Button -->
                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
                    Send Reset Link
                </button>
            </fieldset>
        </form>

        <!-- Back to Login Link -->
        <nav class="mt-8 text-center" aria-label="Back to login">
            <a href="/login"
               class="text-royal-blue hover:text-red font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                ← Back to Login
            </a>
        </nav>
    </article>

    <!-- Back to Home -->
    <nav class="mt-6 text-center" aria-label="Back to home">
        <a href="/"
           class="text-gray-600 hover:text-royal-blue transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
            ← Back to Home
        </a>
    </nav>
</section>

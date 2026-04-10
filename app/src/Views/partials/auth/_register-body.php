<?php
/**
 * Registration form body — rendered inside the shared shell's <main>.
 *
 * @var string $recaptchaSiteKey
 * @var array $errors
 * @var array $oldInput
 */
?>
<?php if (!empty($recaptchaSiteKey)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<a href="#register-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<section class="w-full max-w-md" aria-labelledby="register-heading">
    <article class="bg-white rounded-2xl shadow-lg p-8">
        <header class="text-center mb-8">
            <h1 id="register-heading" class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Create
                Account</h1>
            <p class="text-gray-600">Join Haarlem Festival today</p>
        </header>

        <form action="/register" method="POST">
            <fieldset class="space-y-5">
                <legend class="sr-only">Registration details</legend>

                <section>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            required
                            value="<?php echo htmlspecialchars($oldInput['firstName'] ?? ''); ?>"
                            aria-describedby="first-name-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['firstName']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Enter your first name">
                    <?php if (isset($errors['firstName'])): ?>
                        <p id="first-name-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['firstName']); ?></p>
                    <?php endif; ?>
                </section>

                <section>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            required
                            value="<?php echo htmlspecialchars($oldInput['lastName'] ?? ''); ?>"
                            aria-describedby="last-name-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['lastName']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Enter your last name">
                    <?php if (isset($errors['lastName'])): ?>
                        <p id="last-name-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['lastName']); ?></p>
                    <?php endif; ?>
                </section>

                <section>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            autocomplete="username"
                            value="<?php echo htmlspecialchars($oldInput['username'] ?? ''); ?>"
                            aria-describedby="username-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Choose a username">
                    <?php if (isset($errors['username'])): ?>
                        <p id="username-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['username']); ?></p>
                    <?php endif; ?>
                </section>

                <section>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                            value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>"
                            aria-describedby="email-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Enter your email">
                    <?php if (isset($errors['email'])): ?>
                        <p id="email-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['email']); ?></p>
                    <?php endif; ?>
                </section>

                <section>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            aria-describedby="password-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Create a password (min. 8 characters)">
                    <?php if (isset($errors['password'])): ?>
                        <p id="password-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </section>

                <section>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                        Password</label>
                    <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            required
                            autocomplete="new-password"
                            aria-describedby="confirm-password-error"
                            class="w-full px-4 py-3 border <?php echo isset($errors['confirmPassword']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Confirm your password">
                    <?php if (isset($errors['confirmPassword'])): ?>
                        <p id="confirm-password-error" class="mt-1 text-sm text-red-600"
                           role="alert"><?php echo htmlspecialchars($errors['confirmPassword']); ?></p>
                    <?php endif; ?>
                </section>

                <?php if (!empty($recaptchaSiteKey)): ?>
                    <section aria-labelledby="recaptcha-heading">
                        <h2 id="recaptcha-heading" class="sr-only">reCAPTCHA verification</h2>
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey); ?>"></div>
                        <?php if (isset($errors['captcha'])): ?>
                            <p class="mt-1 text-sm text-red-600"
                               role="alert"><?php echo htmlspecialchars($errors['captcha']); ?></p>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
                    Create Account
                </button>
            </fieldset>
        </form>

        <p class="mt-8 text-center text-gray-600">
            Already have an account?
            <a href="/login"
               class="text-royal-blue hover:text-red font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
                Sign in here
            </a>
        </p>
    </article>

    <nav class="mt-6 text-center" aria-label="Back to home">
        <a href="/"
           class="text-gray-600 hover:text-royal-blue transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 rounded">
            ← Back to Home
        </a>
    </nav>
</section>

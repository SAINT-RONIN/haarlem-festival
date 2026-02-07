<?php
/**
 * Registration page for website visitors.
 *
 * @var string $recaptchaSiteKey Google reCAPTCHA site key
 * @var array $errors Validation errors by field name
 * @var array $oldInput Previously submitted input values
 */
$currentPage = 'register';
$includeNav = true;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- Google reCAPTCHA script -->
<?php if (!empty($recaptchaSiteKey)): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<main class="w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-royal-blue font-serif-display mb-2">Create Account</h1>
                <p class="text-gray-600">Join Haarlem Festival today</p>
            </div>

            <!-- Registration Form -->
            <form action="/register" method="POST" class="space-y-5">
                <!-- First Name -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        First Name
                    </label>
                    <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            required
                            value="<?php echo htmlspecialchars($oldInput['firstName'] ?? ''); ?>"
                            class="w-full px-4 py-3 border <?php echo isset($errors['firstName']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your first name">
                    <?php if (isset($errors['firstName'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['firstName']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Last Name -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Name
                    </label>
                    <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            required
                            value="<?php echo htmlspecialchars($oldInput['lastName'] ?? ''); ?>"
                            class="w-full px-4 py-3 border <?php echo isset($errors['lastName']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your last name">
                    <?php if (isset($errors['lastName'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['lastName']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input
                            type="text"
                            id="username"
                            name="username"
                            required
                            autocomplete="username"
                            value="<?php echo htmlspecialchars($oldInput['username'] ?? ''); ?>"
                            class="w-full px-4 py-3 border <?php echo isset($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Choose a username">
                    <?php if (isset($errors['username'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['username']); ?></p>
                    <?php endif; ?>
                </div>

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
                            value="<?php echo htmlspecialchars($oldInput['email'] ?? ''); ?>"
                            class="w-full px-4 py-3 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Enter your email">
                    <?php if (isset($errors['email'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['email']); ?></p>
                    <?php endif; ?>
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
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border <?php echo isset($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Create a password (min. 8 characters)">
                    <?php if (isset($errors['password'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password
                    </label>
                    <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 border <?php echo isset($errors['confirmPassword']) ? 'border-red-500' : 'border-gray-300'; ?> rounded-lg focus:ring-2 focus:ring-royal-blue focus:border-royal-blue transition-colors"
                            placeholder="Confirm your password">
                    <?php if (isset($errors['confirmPassword'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['confirmPassword']); ?></p>
                    <?php endif; ?>
                </div>

                <!-- Google reCAPTCHA -->
                <?php if (!empty($recaptchaSiteKey)): ?>
                    <div>
                        <div class="g-recaptcha"
                             data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey); ?>"></div>
                        <?php if (isset($errors['captcha'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($errors['captcha']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-8 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="/login" class="text-royal-blue hover:text-red font-medium transition-colors">
                        Sign in here
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


<?php
/**
 * CMS Login page for administrators.
 *
 * @var string|null $error Error message to display
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Login | Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
</head>
<body class="bg-royal-blue min-h-screen flex flex-col items-center justify-center px-4 py-12">
<a href="#cms-login-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
    Skip to main content
</a>

<main id="cms-login-main" class="w-full max-w-md" tabindex="-1">
    <header class="text-center mb-8">
        <a href="/"
           class="inline-flex items-center gap-2 text-sand focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-royal-blue rounded">
            <span class="text-2xl font-medium font-serif">Haarlem Festival</span>
            <img src="/assets/Icons/Logo.svg" alt="" class="w-8 h-8" role="presentation">
        </a>
    </header>

    <article class="bg-white rounded-2xl shadow-lg p-8">
        <header class="text-center mb-8">
            <h1 class="text-3xl font-bold text-royal-blue mb-2">CMS Login</h1>
            <p class="text-gray-600">Administrator access only</p>
        </header>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <section class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" role="alert" aria-live="assertive">
                <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
            </section>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="/cms/login" method="POST">
            <fieldset class="space-y-6">
                <legend class="sr-only">Administrator login credentials</legend>

                <!-- Username or Email -->
                <section>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">Username or Email</label>
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
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2 focus:border-royal-blue transition-colors"
                            placeholder="Enter your password">
                </section>

                <!-- Submit Button -->
                <button
                        type="submit"
                        class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-royal-blue focus-visible:ring-offset-2">
                    Sign In
                </button>
            </fieldset>
        </form>
    </article>

    <!-- Back to Website -->
    <nav class="mt-6 text-center" aria-label="Back to website">
        <a href="/"
           class="text-sand hover:text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-royal-blue rounded">
            ← Back to Website
        </a>
    </nav>
</main>
</body>
</html>


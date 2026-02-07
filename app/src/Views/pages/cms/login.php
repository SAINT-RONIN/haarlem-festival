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
<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="/" class="inline-flex items-center gap-2 text-sand">
            <span class="text-2xl font-medium font-serif">Haarlem Festival</span>
            <img src="/assets/Icons/Logo.svg" alt="" class="w-8 h-8" role="presentation">
        </a>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-royal-blue mb-2">CMS Login</h1>
            <p class="text-gray-600">Administrator access only</p>
        </div>

        <!-- Error Message -->
        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="/cms/login" method="POST" class="space-y-6">
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

            <!-- Submit Button -->
            <button
                    type="submit"
                    class="w-full bg-royal-blue text-white py-3 px-6 rounded-lg font-medium hover:bg-red transition-colors duration-200">
                Sign In
            </button>
        </form>
    </div>

    <!-- Back to Website -->
    <div class="mt-6 text-center">
        <a href="/" class="text-sand hover:text-white transition-colors">
            ← Back to Website
        </a>
    </div>
</div>
</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Haarlem Festival CMS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/errors.css">
</head>
<body>
<div class="error-container">
    <p class="error-code">404</p>
    <h1 class="error-title">Page Not Found</h1>
    <p class="error-message">
        <?= htmlspecialchars($errorMessage ?? 'The page you are looking for does not exist or has been moved.') ?>
    </p>
    <a href="/cms" class="error-back-link">Back to Dashboard</a>
</div>
</body>
</html>


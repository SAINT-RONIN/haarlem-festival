<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Haarlem Festival CMS</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            padding: 2rem;
        }

        .error-code {
            font-size: 6rem;
            font-weight: bold;
            color: #e74c3c;
            margin: 0;
        }

        .error-title {
            font-size: 2rem;
            color: #333;
            margin: 1rem 0;
        }

        .error-message {
            color: #666;
            margin-bottom: 2rem;
            max-width: 500px;
        }

        .error-back-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .error-back-link:hover {
            background-color: #2980b9;
        }
    </style>
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


<?php
/**
 * CMS Dashboard - Main admin panel view.
 *
 * @var array $recentPages Recently updated pages
 * @var array $activities Recent activity feed
 * @var string $currentView Current navigation state
 */

// Ensure we have session data
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_display_name'] ?? 'Administrator';
$currentView = $currentView ?? 'dashboard';
$recentPages = $recentPages ?? [];
$activities = $activities ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Dashboard | Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="flex h-screen">

    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">

        <?php require __DIR__ . '/../../partials/cms/header.php'; ?>

        <main class="flex-1 overflow-y-auto p-6">
            <?php if ($currentView === 'dashboard'): ?>
                <?php require __DIR__ . '/../../partials/cms/dashboard-content.php'; ?>
            <?php elseif ($currentView === 'pages'): ?>
                <?php require __DIR__ . '/../../partials/cms/pages-content.php'; ?>
            <?php endif; ?>
        </main>

    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
</body>
</html>


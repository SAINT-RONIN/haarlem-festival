<?php
/**
 * CMS Dashboard - Main admin panel view.
 *
 * @var \App\ViewModels\Cms\DashboardViewModel|\App\ViewModels\Cms\PagesListViewModel $viewModel
 * @var string $currentView Current navigation state
 */

use App\ViewModels\Cms\DashboardViewModel;
use App\ViewModels\Cms\PagesListViewModel;

$currentView = $currentView ?? 'dashboard';
$viewModel = $viewModel ?? null;

// Extract data from ViewModel for use in partials
if ($viewModel instanceof DashboardViewModel) {
    $recentPages = $viewModel->recentPages;
    $activities = $viewModel->activities;
    $userName = $viewModel->userName;
} elseif ($viewModel instanceof PagesListViewModel) {
    $pages = $viewModel->pages;
    $searchQuery = $viewModel->searchQuery;
    $userName = $viewModel->userName;
} else {
    $userName = 'Administrator';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Dashboard | Haarlem Festival</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/cms.css">
</head>
<body class="bg-gray-50 cms-body">
<a href="#cms-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
    Skip to main content
</a>

<!-- Layout wrapper required for flex sidebar + content -->
<div class="flex h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <section class="flex-1 flex flex-col overflow-hidden" aria-label="CMS content">
        <?php require __DIR__ . '/../../partials/cms/header.php'; ?>

        <main id="cms-main" class="flex-1 overflow-y-auto p-6" tabindex="-1">
            <?php if ($currentView === 'dashboard'): ?>
                <?php require __DIR__ . '/../../partials/cms/dashboard-content.php'; ?>
            <?php elseif ($currentView === 'pages'): ?>
                <?php require __DIR__ . '/../../partials/cms/pages-content.php'; ?>
            <?php endif; ?>
        </main>
    </section>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
</body>
</html>

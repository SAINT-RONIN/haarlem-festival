<?php
/**
 * Shared page shell for CMS pages.
 *
 * Every CMS page builds a CmsPageLayout and calls:
 *   ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
 *       'layout'    => $layout,
 *       'viewModel' => $viewModel,   // optional — only needed when includeFlashMessages is true
 *   ]);
 *
 * @var \App\View\CmsPageLayout $layout
 * @var object|null $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;

$layout ??= new CmsPageLayout(
    title: 'Haarlem CMS',
    currentView: 'dashboard',
    content: new \App\View\ViewTemplate(''),
);
$viewModel ??= null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($layout->title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if ($layout->includeLucide): ?>
        <script src="https://unpkg.com/lucide@0.460.0"></script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml" sizes="any">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/cms.css">
</head>
<body class="bg-gray-50 cms-body min-h-screen">
<a href="#cms-main"
   class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-royal-blue focus:rounded-lg focus:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
    Skip to main content
</a>
<div class="flex min-h-screen">
    <?php ViewRenderer::render(__DIR__ . '/sidebar.php', [
        'currentView' => $layout->currentView,
    ]); ?>

    <main id="cms-main" class="<?= htmlspecialchars($layout->mainClass) ?>" tabindex="-1">
        <?php if ($layout->includeFlashMessages && $viewModel !== null): ?>
            <?php ViewRenderer::render(__DIR__ . '/_flash-messages.php', [
                'viewModel' => $viewModel,
            ]); ?>
        <?php endif; ?>

        <?php ViewRenderer::render($layout->content->path, $layout->content->locals); ?>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
<?php foreach ($layout->extraScripts as $script): ?>
    <script src="<?= htmlspecialchars($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>

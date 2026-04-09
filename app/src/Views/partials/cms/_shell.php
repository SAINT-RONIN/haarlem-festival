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
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php ViewRenderer::render(__DIR__ . '/sidebar.php', [
        'currentView' => $layout->currentView,
    ]); ?>

    <main class="<?= htmlspecialchars($layout->mainClass) ?>">
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

<?php
/**
 * CMS Dashboard - Main admin panel view.
 *
 * @var \App\ViewModels\Cms\DashboardViewModel|\App\ViewModels\Cms\PagesListViewModel $viewModel
 * @var string $currentView Current navigation state
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$currentView ??= 'dashboard';
$viewModel ??= null;

if ($currentView === 'pages') {
    $title = 'Pages - Haarlem CMS';
    $contentPath = __DIR__ . '/../../partials/cms/_pages-content.php';
} else {
    $title = 'Dashboard - Haarlem CMS';
    $contentPath = __DIR__ . '/../../partials/cms/_dashboard-content.php';
}

$layout = new CmsPageLayout(
    title: $title,
    currentView: $currentView,
    content: new ViewTemplate(
        $contentPath,
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: false,
    mainClass: 'flex-1 overflow-y-auto p-6',
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

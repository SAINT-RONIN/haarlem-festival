<?php
/**
 * Lightweight Jazz lineup card form used from the Jazz page editor flow.
 *
 * @var \App\ViewModels\Cms\CmsJazzLineupCardFormViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: htmlspecialchars($viewModel->pageTitle) . ' - Haarlem CMS',
    currentView: 'pages',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/jazz-lineup/_form-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

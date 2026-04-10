<?php
/**
 * CMS Orders list page.
 *
 * @var \App\ViewModels\Cms\CmsOrdersListViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Orders - Haarlem CMS',
    currentView: 'orders',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/orders/_list-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

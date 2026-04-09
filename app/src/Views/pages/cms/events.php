<?php
/**
 * CMS Events list page with weekly schedule overview.
 *
 * @var \App\ViewModels\Cms\CmsEventsListViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Events - Haarlem CMS',
    currentView: 'events',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/events/_list-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

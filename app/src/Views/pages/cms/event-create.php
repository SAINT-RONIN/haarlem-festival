<?php
/**
 * CMS Event create page.
 *
 * @var \App\ViewModels\Cms\CmsEventCreateViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Create Event - Haarlem CMS',
    currentView: 'events',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/events/_create-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
    extraScripts: ['/assets/js/cms/event-create.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

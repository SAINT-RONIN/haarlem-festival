<?php
/**
 * CMS Schedule Days Management Page.
 *
 * @var \App\ViewModels\Cms\CmsScheduleDaysViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Schedule Day Visibility - CMS',
    currentView: 'schedule-days',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/schedule-days/_body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
    mainClass: 'flex-1 p-6',
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

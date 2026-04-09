<?php
/**
 * CMS Media Library page.
 *
 * @var \App\ViewModels\Cms\CmsMediaLibraryViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Media Library - Haarlem CMS',
    currentView: 'media',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/media/_body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
    extraScripts: ['/assets/js/cms/media.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

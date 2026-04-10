<?php
/**
 * CMS Artists list page.
 *
 * @var \App\ViewModels\Cms\CmsArtistsListViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Artists - Haarlem CMS',
    currentView: 'artists',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/artists/_list-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

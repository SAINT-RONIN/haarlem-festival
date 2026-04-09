<?php
/**
 * CMS Users list page.
 *
 * @var \App\ViewModels\Cms\CmsUsersListViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Users - Haarlem CMS',
    currentView: 'users',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/users/_list-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

<?php
/** @var \App\ViewModels\Cms\CmsUserFormViewModel $viewModel */
use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;
$layout = new CmsPageLayout(
    title: htmlspecialchars($viewModel->pageTitle) . ' - Haarlem CMS',
    currentView: 'users',
    content: new ViewTemplate(
        __DIR__ . '/users/_form-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

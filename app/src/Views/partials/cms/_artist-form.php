<?php
/** @var \App\ViewModels\Cms\CmsArtistFormViewModel $viewModel */
use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;
$layout = new CmsPageLayout(
    title: htmlspecialchars($viewModel->pageTitle) . ' - Haarlem CMS',
    currentView: 'artists',
    content: new ViewTemplate(
        __DIR__ . '/artists/_form-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
    extraScripts: [
        'https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js',
        '/assets/js/cms/artist-form.js',
    ],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

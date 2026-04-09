<?php
/**
 * CMS Event edit page.
 *
 * @var \App\ViewModels\Cms\CmsEventEditViewModel $viewModel
 * @var \App\Models\PriceTier[] $priceTiers
 * @var \App\Models\Artist[] $artists
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Edit Event - Haarlem CMS',
    currentView: 'events',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/events/_edit-body.php',
        [
            'viewModel'  => $viewModel,
            'priceTiers' => $priceTiers ?? [],
            'artists'    => $artists ?? [],
        ],
    ),
    includeFlashMessages: true,
    extraScripts: ['/assets/js/cms/event-edit.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

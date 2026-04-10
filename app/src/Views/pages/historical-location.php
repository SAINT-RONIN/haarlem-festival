<?php
/**
 * Generic historical location page view.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/history/detail/_body.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-sand flex flex-col justify-start items-center',
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', [
    'layout' => $layout,
    'viewModel' => $viewModel,
]); ?>

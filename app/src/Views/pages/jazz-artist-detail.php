<?php
/**
 * Generic Jazz artist detail page view.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/jazz/detail/_body.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-sand flex flex-col justify-start items-center',
    currentPage: 'jazz',
    includeNav: false,
    isLoggedIn: false,
    extraScripts: ['/assets/js/add-to-program.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

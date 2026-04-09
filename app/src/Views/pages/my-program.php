<?php
/**
 * My Program page view.
 *
 * @var \App\ViewModels\Program\MyProgramPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/account/_my-program-body.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-[#F5F1EB] min-h-screen',
    currentPage: 'my-program',
    includeNav: true,
    isLoggedIn: $viewModel->isLoggedIn,
    extraScripts: ['/assets/js/my-program.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

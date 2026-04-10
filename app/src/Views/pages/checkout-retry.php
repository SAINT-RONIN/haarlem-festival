<?php
/**
 * Retry payment page — allows users to complete payment for pending orders.
 *
 * @var \App\ViewModels\Program\CheckoutRetryViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/checkout/_retry-body.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-[#F5F1EB] min-h-screen',
    currentPage: $viewModel->currentPage,
    includeNav: true,
    isLoggedIn: $viewModel->isLoggedIn,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

<?php
/**
 * My Orders page view — displays the customer's order history with ticket downloads.
 *
 * @var \App\ViewModels\Program\MyOrdersViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/account/_my-orders-body.php', [
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

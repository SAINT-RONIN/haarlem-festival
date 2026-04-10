<?php
/**
 * Checkout page view.
 *
 * @var \App\ViewModels\Program\CheckoutPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/checkout-main.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-[#F5F1EB] min-h-screen',
    currentPage: 'checkout',
    includeNav: true,
    isLoggedIn: $viewModel->isLoggedIn,
    extraScripts: ['/assets/js/checkout.js?v=' . urlencode($viewModel->checkoutJsVersion)],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

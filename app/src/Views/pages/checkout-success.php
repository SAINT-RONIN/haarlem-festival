<?php
/**
 * Checkout success page view.
 *
 * @var \App\ViewModels\Program\CheckoutSuccessPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/checkout-success-main.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'flex-1 w-full bg-sand py-8 sm:py-10 xl:py-12',
    mainId: 'checkout-success-main',
    mainFocusable: true,
    currentPage: 'checkout',
    includeNav: true,
    isLoggedIn: $viewModel->isLoggedIn,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

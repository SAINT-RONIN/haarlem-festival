<?php
/**
 * Checkout cancellation page view.
 *
 * @var \App\ViewModels\Program\CheckoutCancelPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$primaryHref = $viewModel->hasCancelData && $viewModel->orderId !== 'n/a'
    ? '/checkout/retry/' . $viewModel->orderId
    : '/checkout';

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/checkout-status-card.php', [
            'title' => 'Payment cancelled',
            'message' => 'No worries. Your payment was cancelled and no charge was completed.',
            'details' => $viewModel->hasCancelData
                ? [
                    'Order' => $viewModel->orderId,
                    'Payment' => $viewModel->paymentId,
                ]
                : [],
            'primaryHref' => $primaryHref,
            'primaryLabel' => 'Try again',
            'secondaryHref' => '/my-program',
            'secondaryLabel' => 'Back to my program',
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-[#F5F1EB] min-h-screen px-4 sm:px-8 lg:px-24 py-12',
    currentPage: 'checkout',
    includeNav: true,
    isLoggedIn: $viewModel->isLoggedIn,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

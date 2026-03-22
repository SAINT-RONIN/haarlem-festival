<?php
/**
 * @var \App\ViewModels\Program\CheckoutSuccessPageViewModel $viewModel
 */
$currentPage = 'checkout';
$includeNav = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$title = 'Payment submitted';
$message = 'Thank you. Your payment is being finalized by Stripe. You will receive confirmation shortly.';
$details = $viewModel->hasSessionData
    ? [
        'Session' => $viewModel->sessionId,
        'Stripe status' => $viewModel->paymentStatus,
        'Checkout state' => $viewModel->checkoutStatus,
    ]
    : [];
$primaryHref = '/';
$primaryLabel = 'Back to home';
$secondaryHref = '/my-program';
$secondaryLabel = 'View my program';
require __DIR__ . '/../partials/checkout-status-card.php';
?>

<?php require __DIR__ . '/../partials/footer.php'; ?>


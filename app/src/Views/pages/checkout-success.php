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
$details = $viewModel->sessionSummary !== null
    ? [
        'Session' => (string)($viewModel->sessionSummary['sessionId'] ?? ''),
        'Stripe status' => (string)($viewModel->sessionSummary['paymentStatus'] ?? 'unknown'),
        'Checkout state' => (string)($viewModel->sessionSummary['status'] ?? 'unknown'),
    ]
    : [];
$primaryHref = '/';
$primaryLabel = 'Back to home';
$secondaryHref = '/my-program';
$secondaryLabel = 'View my program';
require __DIR__ . '/../partials/checkout-status-card.php';
?>

<?php require __DIR__ . '/../partials/footer.php'; ?>


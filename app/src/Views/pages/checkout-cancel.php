<?php
/**
 * @var \App\ViewModels\Program\CheckoutCancelPageViewModel $viewModel
 */
$currentPage = 'checkout';
$includeNav = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php
$title = 'Payment cancelled';
$message = 'No worries. Your payment was cancelled and no charge was completed.';
$details = $viewModel->hasCancelData
    ? [
        'Order' => $viewModel->orderId,
        'Payment' => $viewModel->paymentId,
    ]
    : [];
$primaryHref = '/checkout';
$primaryLabel = 'Try again';
$secondaryHref = '/my-program';
$secondaryLabel = 'Back to my program';
require __DIR__ . '/../partials/checkout-status-card.php';
?>

<?php require __DIR__ . '/../partials/footer.php'; ?>


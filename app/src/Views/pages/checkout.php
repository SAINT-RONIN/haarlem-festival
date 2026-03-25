<?php
/**
 * Checkout page view.
 *
 * @var \App\ViewModels\Program\CheckoutPageViewModel $viewModel
 */
$currentPage = 'checkout';
$includeNav = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php require __DIR__ . '/../partials/checkout-main.php'; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/checkout.js?v=<?= urlencode($viewModel->checkoutJsVersion) ?>"></script>

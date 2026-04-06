<?php
/**
 * @var \App\ViewModels\Program\CheckoutSuccessPageViewModel $viewModel
 */
use App\View\ViewRenderer;

$currentPage = 'checkout';
$includeNav = true;
$useLayoutWrapper = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>
<?php ViewRenderer::render(__DIR__ . '/../partials/checkout-success-main.php', ['viewModel' => $viewModel]); ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>

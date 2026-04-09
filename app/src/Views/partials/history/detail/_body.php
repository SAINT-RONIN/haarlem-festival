<?php
/**
 * Historical location detail page body — renders all detail sections sequentially.
 *
 * Currently uses `require` rather than ViewRenderer::render because the inner
 * `history/detail/hero-section.php` reads navbar-related locals from the
 * surrounding scope. Phase 6 of the view refactor will convert these to
 * explicit ViewRenderer::render calls.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

$currentPage = $viewModel->currentPage;
$isLoggedIn = $viewModel->globalUi->isLoggedIn;
?>
<?php require __DIR__ . '/hero-section.php'; ?>
<?php require __DIR__ . '/intro-section.php'; ?>
<?php require __DIR__ . '/facts-section.php'; ?>
<?php require __DIR__ . '/significance-section.php'; ?>

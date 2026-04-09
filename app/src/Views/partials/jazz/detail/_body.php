<?php
/**
 * Jazz artist detail page body — renders all detail sections sequentially.
 *
 * Currently uses `require` rather than ViewRenderer::render because the inner
 * `jazz/detail/hero-section.php` includes navbar via `require`, and navbar
 * reads $currentPage/$isLoggedIn from the surrounding scope. Phase 6 of the
 * view refactor will convert these to explicit ViewRenderer::render calls.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */

// Navbar inside hero-section.php reads these from scope — preserve legacy values.
$currentPage = 'jazz';
$isLoggedIn = false;
?>
<?php require __DIR__ . '/hero-section.php'; ?>
<?php require __DIR__ . '/overview-section.php'; ?>
<?php require __DIR__ . '/photo-gallery-section.php'; ?>
<?php require __DIR__ . '/featured-albums-section.php'; ?>
<?php require __DIR__ . '/listen-now-section.php'; ?>
<?php require __DIR__ . '/live-cta-section.php'; ?>
<?php require __DIR__ . '/performances-section.php'; ?>

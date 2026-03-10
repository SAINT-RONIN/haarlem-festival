<?php
/**
 * Generic Jazz artist detail page view.
 *
 * @var \App\ViewModels\Jazz\JazzArtistDetailPageViewModel $viewModel
 */

$currentPage = 'jazz';
$includeNav = false;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand flex flex-col justify-start items-center">
    <?php require __DIR__ . '/../partials/jazz/detail/hero-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/overview-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/photo-gallery-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/featured-albums-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/listen-now-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/live-cta-section.php'; ?>
    <?php require __DIR__ . '/../partials/jazz/detail/performances-section.php'; ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

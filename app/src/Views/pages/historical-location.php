<?php
/**
 * Generic historical location page view.
 *
 * @var \App\ViewModels\History\HistoricalLocationViewModel $viewModel
 */

$currentPage = 'history';
$includeNav = false;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand flex flex-col justify-start items-center">
    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/history/detail/intro-section.php'; ?>
    <?php require __DIR__ . '/../partials/history/detail/facts-section.php'; ?>
    <?php require __DIR__ . '/../partials/history/detail/significance-section.php'; ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>


<?php
/**
 * History page view.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

    <?php require __DIR__ . '/../partials/history/route-section.php'; ?>

    <?php require __DIR__ . '/../partials/history/locations-section.php'; ?>

    <?php require __DIR__ . '/../partials/history/ticket-options.php'; ?>

    <?php require __DIR__ . '/../partials/history/info-about-tour.php'; ?>

    <?php require __DIR__ . '/../partials/history/schedule-section.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>

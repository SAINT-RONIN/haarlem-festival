<?php
/**
 * Jazz page view.
 *
 * @var \App\ViewModels\Jazz\JazzPageViewModel $viewModel
 */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

    <?php require __DIR__ . '/../partials/jazz/venues-section.php'; ?>

    <?php require __DIR__ . '/../partials/jazz/pricing-section.php'; ?>

    <?php require __DIR__ . '/../partials/jazz/schedule-cta-section.php'; ?>

    <?php require __DIR__ . '/../partials/jazz/artists-section.php'; ?>

    <?php require __DIR__ . '/../partials/jazz/booking-cta-section.php'; ?>

    <?php if ($viewModel->scheduleSection !== null): ?>
        <?php require __DIR__ . '/../partials/sections/schedule/schedule-section.php'; ?>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

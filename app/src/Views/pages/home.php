<?php
/**
 * Homepage view - Main landing page for Haarlem Festival.
 *
 * @var \App\ViewModels\HomePageViewModel $viewModel Contains all page data
 */

// Extract data from viewModel for use in partials
$eventTypes = $viewModel->eventTypes;
$locations = $viewModel->locations;
$scheduleDays = $viewModel->scheduleDays;
$currentPage = 'home';

// For homepage, navigation is inside hero section (not separate)
$includeNav = false;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-stone-100 inline-flex flex-col justify-start items-center overflow-hidden">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/explore-banner.php'; ?>

    <?php require __DIR__ . '/../partials/intro-section.php'; ?>

    <?php require __DIR__ . '/../partials/events-showcase-section.php'; ?>

    <?php require __DIR__ . '/../partials/locations-section.php'; ?>

    <?php require __DIR__ . '/../partials/schedule-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>


<?php
/**
 * History page view.
 *
 * @var \App\ViewModels\StorytellingPageViewModel $viewModel
 */
/**
 * @var \App\ViewModels\HomePageViewModel $viewModel
 */

$eventTypes = $viewModel->eventTypes;
$locations = $viewModel->locations;
$scheduleDays = $viewModel->scheduleDays;
$cms = $viewModel->cmsContent;
$currentPage = 'home';
$includeNav = false;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/explore-banner.php'; ?>

    <?php require __DIR__ . '/../partials/intro-section.php'; ?>

    <?php require __DIR__ . '/../partials/events-showcase-section.php'; ?>

    <?php require __DIR__ . '/../partials/locations-section.php'; ?>

    <?php require __DIR__ . '/../partials/schedule-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

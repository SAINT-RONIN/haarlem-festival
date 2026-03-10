<?php
/**
 * @var \App\ViewModels\HomePageViewModel $viewModel
 */

$eventTypes = $viewModel->eventTypes;
$locations = $viewModel->locations;
$scheduleDays = $viewModel->scheduleDays;
$pageContentPartials = [
    __DIR__ . '/../partials/explore-banner.php',
    __DIR__ . '/../partials/intro-section.php',
    __DIR__ . '/../partials/events-showcase-section.php',
    __DIR__ . '/../partials/locations-section.php',
    __DIR__ . '/../partials/schedule-section.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>

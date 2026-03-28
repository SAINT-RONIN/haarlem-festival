<?php
/**
 * Restaurant page view.
 *
 * @var \App\ViewModels\Restaurant\RestaurantPageViewModel $viewModel
 */

$includeEventSections = true;
$eventIntroSectionId = 'about';
$pageContentPartials = [
    __DIR__ . '/../partials/restaurant/content-sections.php',
];
?>
<?php
$extraScripts = ['/assets/js/restaurant-filters.js'];
require __DIR__ . '/../partials/_shell.php';
?>

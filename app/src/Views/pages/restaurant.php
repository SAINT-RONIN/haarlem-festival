<?php

declare(strict_types=1);

/**
 * Restaurant page view.
 *
 * CMS-driven (DB-first) via RestaurantPageService → RestaurantPageViewModel.
 * Falls back to hardcoded values inside the service unless ?source=db is used.
 */

/** @var \App\ViewModels\RestaurantPageViewModel $viewModel */

$includeEventSections = true;
$eventIntroSectionId = 'about';
$pageContentPartials = [
    __DIR__ . '/../partials/restaurant/content-sections.php',
];
?>
<?php require __DIR__ . '/../partials/_shell.php'; ?>

<?php
/**
 * Restaurant page-specific content sections rendered inside the shared shell.
 *
 * @var \App\ViewModels\Restaurant\RestaurantPageViewModel $viewModel
 */

if ($viewModel->introSplit2Section !== null) {
    $introSplitSection = $viewModel->introSplit2Section;
    $sectionId = 'restaurants';
    $introSplitImageClass = null;
    $introSplitReversed = true;
    require __DIR__ . '/../sections/intro-split-section.php';
    $introSplitReversed = false;  // Reset so it doesn't affect other sections
}

if ($viewModel->instructionsSection !== null) {
    $instructionsSection = $viewModel->instructionsSection;
    require __DIR__ . '/instructions-section.php';
}

if ($viewModel->restaurantCardsSection !== null) {
    $restaurantCardsSection = $viewModel->restaurantCardsSection;
    require __DIR__ . '/restaurant-cards-section.php';
}

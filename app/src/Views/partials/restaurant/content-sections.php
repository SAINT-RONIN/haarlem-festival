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
    require __DIR__ . '/intro-split2-restaurant-section.php';
}

if ($viewModel->instructionsSection !== null) {
    $instructionsSection = $viewModel->instructionsSection;
    require __DIR__ . '/instructions-section.php';
}

if ($viewModel->restaurantCardsSection !== null) {
    $restaurantCardsSection = $viewModel->restaurantCardsSection;
    require __DIR__ . '/restaurant-cards-section.php';
}

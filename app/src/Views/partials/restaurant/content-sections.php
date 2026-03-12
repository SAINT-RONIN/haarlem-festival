<?php
/**
 * Restaurant page-specific content sections rendered inside the shared shell.
 *
 * Expects a \App\ViewModels\Restaurant\RestaurantPageViewModel as $viewModel
 * and uses its introSplit2Section, instructionsSection, and restaurantCardsSection properties.
 *
 * @var \App\ViewModels\Restaurant\RestaurantPageViewModel $viewModel
 */

use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;

if ($viewModel->introSplit2Section !== null) {
    /** @var IntroSplitSectionData $introSplitSection */
    $introSplitSection = $viewModel->introSplit2Section;
    $sectionId = 'restaurants';
    $introSplitImageClass = null;
    $introSplitReversed = true;
    require __DIR__ . '/../sections/intro-split-section.php';
    $introSplitReversed = false;  // Reset so it doesn't affect other sections
}

if ($viewModel->instructionsSection !== null) {
    /** @var InstructionsSectionData $instructionsSection */
    $instructionsSection = $viewModel->instructionsSection;
    require __DIR__ . '/instructions-section.php';
}

if ($viewModel->restaurantCardsSection !== null) {
    /** @var RestaurantCardsSectionData $restaurantCardsSection */
    $restaurantCardsSection = $viewModel->restaurantCardsSection;
    require __DIR__ . '/restaurant-cards-section.php';
}

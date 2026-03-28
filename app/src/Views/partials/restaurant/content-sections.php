<?php
/**
 * Restaurant page-specific content sections rendered inside the shared shell.
 *
 * @var \App\ViewModels\Restaurant\RestaurantPageViewModel $viewModel
 */

use App\View\ViewRenderer;

if ($viewModel->introSplit2Section !== null) {
    ViewRenderer::render(__DIR__ . '/../sections/intro-split-section.php', [
        'introSplitSection' => $viewModel->introSplit2Section,
        'sectionId' => 'restaurants',
        'introSplitImageClass' => null,
        'introSplitReversed' => true,
    ]);
}

if ($viewModel->instructionsSection !== null) {
    ViewRenderer::render(__DIR__ . '/instructions-section.php', [
        'instructionsSection' => $viewModel->instructionsSection,
    ]);
}

if ($viewModel->restaurantCardsSection !== null) {
    ViewRenderer::render(__DIR__ . '/restaurant-cards-section.php', [
        'restaurantCardsSection' => $viewModel->restaurantCardsSection,
    ]);
}

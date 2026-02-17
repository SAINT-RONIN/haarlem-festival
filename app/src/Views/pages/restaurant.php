<?php

declare(strict_types=1);

/**
 * Restaurant page view.
 *
 * CMS-driven (DB-first) via RestaurantPageService → RestaurantPageViewModel.
 * Falls back to hardcoded values inside the service unless ?source=db is used.
 */

/** @var \App\ViewModels\RestaurantPageViewModel $viewModel */
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php $sectionId = 'about';
$introSplitImageClass = null;
require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

    <?php if ($viewModel->introSplit2Section !== null): ?>
        <?php
        $introSplitSection = $viewModel->introSplit2Section;
        $sectionId = 'restaurants';
        $introSplitImageClass = null;
        require __DIR__ . '/../partials/restaurant/intro-split2-restaurant-section.php';
        ?>
    <?php endif; ?>

    <?php if ($viewModel->instructionsSection !== null): ?>
        <?php $instructionsSection = $viewModel->instructionsSection;
        require __DIR__ . '/../partials/restaurant/instructions-section.php'; ?>
    <?php endif; ?>

    <?php if ($viewModel->restaurantCardsSection !== null): ?>
        <?php $restaurantCardsSection = $viewModel->restaurantCardsSection;
        require __DIR__ . '/../partials/restaurant/restaurant-cards-section.php'; ?>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

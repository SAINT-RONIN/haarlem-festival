<?php

declare(strict_types=1);

/** @var \App\ViewModels\Dance\DancePageViewModel $viewModel */

$heroData = $viewModel->heroData;
$globalUi = $viewModel->globalUi;
$gradientSection = $viewModel->gradientSection;
$introSplitSection = $viewModel->introSplitSection;
$experienceData = $viewModel->experienceData;

$currentPage = 'dance';
$includeNav = true;

$cms = [
    'hero_section' => [
        'hero_main_title' => $heroData->mainTitle,
        'hero_subtitle' => $heroData->subtitle,
        'hero_button_primary' => $heroData->primaryButtonText,
        'hero_button_primary_link' => $heroData->primaryButtonLink,
        'hero_button_secondary' => $heroData->secondaryButtonText,
        'hero_button_secondary_link' => $heroData->secondaryButtonLink,
        'hero_background_image' => $heroData->backgroundImageUrl,
    ],
    'global_ui' => [
        'site_name' => $globalUi->siteName,
        'nav_home' => $globalUi->navHome,
        'nav_jazz' => $globalUi->navJazz,
        'nav_dance' => $globalUi->navDance,
        'nav_history' => $globalUi->navHistory,
        'nav_restaurant' => $globalUi->navRestaurant,
        'nav_storytelling' => $globalUi->navStorytelling,
        'btn_my_program' => $globalUi->btnMyProgram,
    ],
];

require __DIR__ . '/../partials/header.php';
?>

<main class="w-full bg-sand flex flex-col">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/dance/intro-highlight-section.php'; ?>

    <?php require __DIR__ . '/../partials/dance/headliners-section.php'; ?>

    <?php require __DIR__ . '/../partials/dance/supporting-artists-section.php'; ?>

    <?php require __DIR__ . '/../partials/dance/dance-schedule-section.php'; ?>

    <?php require __DIR__ . '/../partials/dance/experience-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
<?php
/**
 * Storytelling page view.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */

$heroData = $viewModel->heroData;
$globalUi = $viewModel->globalUi;
$gradientSection = $viewModel->gradientSection;
$introSplitSection = $viewModel->introSplitSection;
$masonrySection = $viewModel->masonrySection;
$scheduleSection = $viewModel->scheduleSection;
$currentPage = 'storytelling';
$includeNav = false;

// Build $cms array for hero.php partial compatibility
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
                'is_logged_in' => $globalUi->isLoggedIn,
        ],
];
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-sand inline-flex flex-col justify-start items-center">

    <?php require __DIR__ . '/../partials/hero.php'; ?>

    <?php require __DIR__ . '/../partials/sections/gradient-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/intro-split-section.php'; ?>

    <?php require __DIR__ . '/../partials/storytelling-masonry-section.php'; ?>

    <?php require __DIR__ . '/../partials/sections/schedule/schedule-section.php'; ?>

</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

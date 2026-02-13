<?php
/**
 * History page view.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

$heroData = $viewModel->heroData;
$globalUi = $viewModel->globalUi;
$gradientSection = $viewModel->gradientSection;
$introSplitSection = $viewModel->introSplitSection;
$routeData = $viewModel->routeData;
$venuesData = $viewModel->venuesData;
$ticketOptionsData = $viewModel->ticketOptionsData;
$infoAboutTourData = $viewModel->infoAboutTourData;
$scheduleData = $viewModel->scheduleData;
$currentPage = 'history';
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

    <?php require __DIR__ . '/../partials/history/locations-section.php'; ?>


        <div class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-2.5 overflow-hidden">
            <div class="justify-center text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">Read more about these locations</div>
            <div class="w-full inline-flex justify-start items-stretch gap-12">
                <?php /* Card 1: Grote Markt */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>

                <?php /* Card 2: Amsterdamse Poort */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>

                <?php /* Card 3: Molen De Adriaan */ ?>
                <?php require __DIR__ . '/../partials/history-location-card.php'; ?>
            </div>
        </div>
        <div class="self-stretch px-6 lg:px-24 py-12 flex flex-col justify-center items-center gap-12 overflow-hidden">
            <div class="self-stretch justify-start text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
                Your ticket options to join the experience
            </div>
            <div class="self-stretch inline-flex justify-center items-center gap-48">
                <?php require __DIR__ . '/../partials/history-ticket-option.php'; ?>
                <?php require __DIR__ . '/../partials/history-ticket-option.php'; ?>

            </div>
        </div>
        <div class="self-stretch px-6 lg:px-24 py-12 inline-flex flex-col justify-start items-start gap-6 overflow-hidden">
            <div class="self-stretch flex flex-col justify-start items-start gap-6">
                <div class="inline-flex justify-center items-center gap-2.5">
                    <div class="justify-center text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
                        Important information about the tour
                    </div>
                </div>
                <div class="self-stretch inline-flex justify-start items-start">
                    <div class="tour-info flex-1 justify-start text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
                        <ul class="list-disc pl-6 space-y-1">
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                            <li><?php require __DIR__ . '/../partials/history-tour-info-item.php'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php require __DIR__ . '/../partials/history/schedule-section.php'; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>

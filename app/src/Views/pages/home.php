<?php
/**
 * @var \App\ViewModels\HomePageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(contentTemplates: [
    new ViewTemplate(__DIR__ . '/../partials/explore-banner.php', [
        'exploreBanner' => $viewModel->exploreBanner,
    ]),
    new ViewTemplate(__DIR__ . '/../partials/intro-section.php', [
        'introSection' => $viewModel->introSection,
    ]),
    new ViewTemplate(__DIR__ . '/../partials/events-showcase-section.php', [
        'eventsHeader' => $viewModel->eventsHeader,
        'eventTypes' => $viewModel->eventTypes,
    ]),
    new ViewTemplate(__DIR__ . '/../partials/locations-section.php', [
        'locationsSection' => $viewModel->locationsSection,
        'locations' => $viewModel->locations,
    ]),
    new ViewTemplate(__DIR__ . '/../partials/schedule-section.php', [
        'schedulePreviewSection' => $viewModel->schedulePreviewSection,
        'scheduleDays' => $viewModel->scheduleDays,
        'globalUi' => $viewModel->globalUi,
    ]),
]);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

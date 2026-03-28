<?php
/**
 * Jazz page view.
 *
 * @var \App\ViewModels\Jazz\JazzPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$contentTemplates = [
    new ViewTemplate(__DIR__ . '/../partials/jazz/venues-section.php', ['venuesData' => $viewModel->venuesData]),
    new ViewTemplate(__DIR__ . '/../partials/jazz/pricing-section.php', ['pricingData' => $viewModel->pricingData]),
    new ViewTemplate(__DIR__ . '/../partials/jazz/schedule-cta-section.php', ['scheduleCtaData' => $viewModel->scheduleCtaData]),
    new ViewTemplate(__DIR__ . '/../partials/jazz/artists-section.php', ['artistsData' => $viewModel->artistsData]),
    new ViewTemplate(__DIR__ . '/../partials/jazz/booking-cta-section.php', ['bookingCtaData' => $viewModel->bookingCtaData]),
];
if ($viewModel->scheduleSection !== null) {
    $contentTemplates[] = new ViewTemplate(
        __DIR__ . '/../partials/sections/schedule/schedule-section.php',
        ['scheduleSection' => $viewModel->scheduleSection],
    );
}

$layout = new PublicPageLayout(
    contentTemplates: $contentTemplates,
    includeEventSections: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

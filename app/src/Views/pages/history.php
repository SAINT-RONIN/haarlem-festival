<?php
/**
 * History page view.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/history/route-section.php'),
        new ViewTemplate(__DIR__ . '/../partials/history/locations-section.php'),
        new ViewTemplate(__DIR__ . '/../partials/history/ticket-options.php'),
        new ViewTemplate(__DIR__ . '/../partials/history/info-about-tour.php'),
        new ViewTemplate(__DIR__ . '/../partials/sections/schedule/schedule-section.php', [
            'scheduleSection' => $viewModel->scheduleSection,
        ]),
    ],
    includeEventSections: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

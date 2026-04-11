<?php
/**
 * Dance page view.
 *
 * @var \App\ViewModels\Dance\DancePageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$contentTemplates = [
    new ViewTemplate(__DIR__ . '/../partials/dance/headliners-section.php', ['headlinersData' => $viewModel->headlinersData]),
    new ViewTemplate(__DIR__ . '/../partials/dance/supporting-artists-section.php', ['supportingArtistsData' => $viewModel->supportingArtistsData]),
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

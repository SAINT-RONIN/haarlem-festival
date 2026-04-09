<?php
/**
 * Renders the main Storytelling overview page.
 * The reason for this is because it acts as the entry point for the Storytelling
 * event type, composing the shell, masonry image grid, and schedule into one page.
 *
 * @var \App\ViewModels\Storytelling\StorytellingPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$primaryLink = $viewModel->heroData->primaryButtonLink;
$derivedIntroSectionId = 'intro';
$primaryLinkFragment = parse_url($primaryLink, PHP_URL_FRAGMENT);
if (is_string($primaryLinkFragment) && $primaryLinkFragment !== '') {
    // Keep IDs safe for HTML by allowing only common id characters.
    $normalizedId = preg_replace('/[^A-Za-z0-9\-_:.]/', '', $primaryLinkFragment);
    if (is_string($normalizedId) && $normalizedId !== '') {
        $derivedIntroSectionId = $normalizedId;
    }
}

$layout = new PublicPageLayout(
    contentTemplates: [
        // Displays the masonry image grid that introduces the storytelling event visually.
        new ViewTemplate(__DIR__ . '/../partials/storytelling-masonry-section.php'),
        // Displays the full session schedule so visitors can browse available dates and times.
        new ViewTemplate(__DIR__ . '/../partials/sections/schedule/schedule-section.php', [
            'scheduleSection' => $viewModel->scheduleSection,
        ]),
    ],
    includeEventSections: true,
    eventIntroSectionId: $derivedIntroSectionId,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

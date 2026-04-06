<?php
/**
 * Renders the detail page for a single Storytelling event (artist/show).
 * The reason for this is because each storytelling event needs its own dedicated page
 * with a custom hero, rich content sections, and a filtered session schedule —
 * a layout distinct from the shared overview page.
 *
 * @var \App\ViewModels\Storytelling\StorytellingDetailPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        // Custom full-screen hero with inline nav — replaces the shared hero.php partial.
        new ViewTemplate(__DIR__ . '/../partials/storytelling/detail-hero.php'),
        // Renders the about, highlights, gallery, and video sections for this event.
        new ViewTemplate(__DIR__ . '/../partials/storytelling/detail-content.php'),
        // Renders the session schedule scoped to this specific storytelling event.
        new ViewTemplate(__DIR__ . '/../partials/storytelling/detail-schedule.php'),
    ],
    includeHero: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

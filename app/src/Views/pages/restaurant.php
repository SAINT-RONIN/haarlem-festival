<?php
/**
 * Restaurant page view.
 *
 * @var \App\ViewModels\Restaurant\RestaurantPageViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/restaurant/content-sections.php'),
    ],
    includeEventSections: true,
    eventIntroSectionId: 'about',
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

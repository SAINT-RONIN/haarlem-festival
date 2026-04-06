<?php
/**
 * Restaurant reservation form page.
 *
 * @var \App\ViewModels\Restaurant\RestaurantDetailViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(contentTemplates: [
    new ViewTemplate(__DIR__ . '/../partials/restaurant/reservation-form.php'),
]);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['viewModel' => $viewModel, 'layout' => $layout]); ?>

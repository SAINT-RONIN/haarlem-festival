<?php
/**
 * CMS Venues management page.
 *
 * @var \App\Models\Venue[] $venues
 * @var ?string $successMessage
 * @var ?string $errorMessage
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Venues - Haarlem CMS',
    currentView: 'venues',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/venues/_list-body.php',
        [
            'venues'         => $venues ?? [],
            'successMessage' => $successMessage ?? null,
            'errorMessage'   => $errorMessage ?? null,
        ],
    ),
    includeFlashMessages: true,
    extraScripts: ['/assets/js/cms/venues.js'],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => null,
]); ?>

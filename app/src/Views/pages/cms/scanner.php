<?php
/**
 * Ticket Scanner page — QR code scanning for venue entry validation.
 *
 * @var string $currentView Current navigation state for sidebar highlighting.
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$currentView ??= 'scanner';

$layout = new CmsPageLayout(
    title: 'Ticket Scanner — Haarlem CMS',
    currentView: $currentView,
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/scanner/_body.php',
        ['currentView' => $currentView],
    ),
    includeFlashMessages: false,
    extraScripts: [
        'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js',
        '/assets/js/cms/scanner.js',
    ],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => null,
]); ?>

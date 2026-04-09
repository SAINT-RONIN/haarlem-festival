<?php
/**
 * CMS Login page for administrators.
 *
 * @var string|null $error Error message to display
 */

use App\View\ViewRenderer;
use App\View\ViewTemplate;

$content = new ViewTemplate(
    __DIR__ . '/../../partials/cms/login/_body.php',
    ['error' => $error ?? null],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_login-shell.php', [
    'content' => $content,
]); ?>

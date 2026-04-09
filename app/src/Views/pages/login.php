<?php
/**
 * Login page for website visitors.
 *
 * @var string|null $error Error message to display
 * @var string|null $success Success message to display
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/auth/_login-body.php', [
            'error' => $error ?? null,
            'success' => $success ?? null,
        ]),
    ],
    includeHero: false,
    mainClass: 'flex-1 w-full bg-sand flex flex-col items-center justify-center px-4 py-12',
    mainId: 'login-main',
    mainFocusable: true,
    currentPage: 'login',
    includeNav: true,
    isLoggedIn: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

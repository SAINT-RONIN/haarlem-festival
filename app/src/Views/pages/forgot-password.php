<?php
/**
 * Forgot password page for website visitors.
 *
 * @var string|null $success Success message to display
 * @var string|null $error Error message to display
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/auth/_forgot-password-body.php', [
            'success' => $success ?? null,
            'error' => $error ?? null,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12',
    mainId: 'forgot-password-main',
    mainFocusable: true,
    currentPage: 'forgot-password',
    includeNav: true,
    isLoggedIn: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

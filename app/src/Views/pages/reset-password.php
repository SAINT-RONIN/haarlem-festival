<?php
/**
 * Reset password page for website visitors.
 *
 * @var string $token The reset token from the URL (passed from controller)
 * @var bool $validToken Whether the token is valid
 * @var string|null $error Error message to display
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/auth/_reset-password-body.php', [
            'token' => $token ?? '',
            'validToken' => $validToken ?? false,
            'error' => $error ?? null,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12',
    mainId: 'reset-password-main',
    mainFocusable: true,
    currentPage: 'reset-password',
    includeNav: true,
    isLoggedIn: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

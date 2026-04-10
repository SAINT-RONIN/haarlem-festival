<?php
/**
 * Registration page for website visitors.
 *
 * @var string $recaptchaSiteKey Google reCAPTCHA site key
 * @var array $errors Validation errors by field name
 * @var array $oldInput Previously submitted input values
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/auth/_register-body.php', [
            'recaptchaSiteKey' => $recaptchaSiteKey ?? '',
            'errors' => $errors ?? [],
            'oldInput' => $oldInput ?? [],
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full min-h-screen bg-sand flex flex-col items-center justify-center px-4 py-12',
    mainId: 'register-main',
    mainFocusable: true,
    currentPage: 'register',
    includeNav: true,
    isLoggedIn: false,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>

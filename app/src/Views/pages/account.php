<?php
/**
 * Account management page - allows authenticated users to edit profile, email, password, and picture.
 *
 * @var \App\ViewModels\Account\AccountFormViewModel $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new PublicPageLayout(
    contentTemplates: [
        new ViewTemplate(__DIR__ . '/../partials/account/_edit-profile.php', [
            'viewModel' => $viewModel,
        ]),
    ],
    includeHero: false,
    mainClass: 'w-full bg-sand inline-flex flex-col justify-start items-center',
    currentPage: 'account',
    includeNav: true,
    isLoggedIn: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../partials/_shell.php', ['layout' => $layout]); ?>




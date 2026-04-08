<?php
/**
 * Shared page shell for public pages that use BaseViewModel.
 *
 * Required:
 * @var \App\ViewModels\BaseViewModel $viewModel
 * @var \App\View\PublicPageLayout $layout
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;

/** @var BaseViewModel $viewModel — guaranteed by the controller's renderPage() method */
/** @var PublicPageLayout $layout */

$layout ??= new PublicPageLayout();

$heroData = $viewModel->heroData;
$globalUi = $viewModel->globalUi;
$currentPage = $viewModel->currentPage;
$includeNav = $viewModel->includeNav;
$isLoggedIn = $globalUi->isLoggedIn;
$gradientSection = property_exists($viewModel, 'gradientSection') && $viewModel->gradientSection instanceof GradientSectionData
    ? $viewModel->gradientSection
    : null;
$introSplitSection = property_exists($viewModel, 'introSplitSection') && $viewModel->introSplitSection instanceof IntroSplitSectionData
    ? $viewModel->introSplitSection
    : null;
?>

<?php ViewRenderer::render(__DIR__ . '/header.php', [
    'currentPage' => $currentPage,
    'includeNav' => $includeNav,
    'isLoggedIn' => $isLoggedIn,
]); ?>

<main class="<?= htmlspecialchars($layout->mainClass) ?>">
    <?php if ($layout->includeHero): ?>
        <?php ViewRenderer::render(__DIR__ . '/hero.php', [
            'heroData' => $heroData,
            'globalUi' => $globalUi,
            'currentPage' => $currentPage,
            'isLoggedIn' => $isLoggedIn,
        ]); ?>
    <?php endif; ?>

    <?php if ($layout->includeEventSections): ?>
        <?php if ($gradientSection !== null): ?>
            <?php ViewRenderer::render(__DIR__ . '/sections/gradient-section.php', [
                'gradientSection' => $gradientSection,
            ]); ?>
        <?php endif; ?>

        <?php if ($introSplitSection !== null): ?>
            <?php ViewRenderer::render(__DIR__ . '/sections/intro-split-section.php', [
                'introSplitSection' => $introSplitSection,
                'sectionId' => $layout->eventIntroSectionId,
                'introSplitImageClass' => $layout->eventIntroImageClass,
            ]); ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php foreach ($layout->contentTemplates as $contentTemplate): ?>
        <?php ViewRenderer::render($contentTemplate->path, $contentTemplate->locals + ['viewModel' => $viewModel]); ?>
    <?php endforeach; ?>
</main>

<?php ViewRenderer::render(__DIR__ . '/footer.php', ['globalUi' => $globalUi, 'currentPage' => $currentPage]); ?>

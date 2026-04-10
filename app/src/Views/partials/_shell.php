<?php
/**
 * Shared page shell for public pages.
 *
 * Two ways to supply shell data:
 *   1. Pass a $viewModel extending BaseViewModel (heroData/globalUi/currentPage/includeNav
 *      are read off it). This is the backward-compatible path used by home, history,
 *      jazz, storytelling, restaurant etc.
 *   2. Pass the fields explicitly on $layout (currentPage, globalUi, heroData, etc.)
 *      for pages that don't have a BaseViewModel viewmodel (auth, checkout, account).
 *
 * Values on $layout take precedence over values on $viewModel.
 *
 * @var \App\View\PublicPageLayout $layout
 * @var \App\ViewModels\BaseViewModel|null $viewModel
 */

use App\View\PublicPageLayout;
use App\View\ViewRenderer;
use App\ViewModels\BaseViewModel;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;

$layout ??= new PublicPageLayout();
$viewModel ??= null;
$shellViewModel = $viewModel instanceof BaseViewModel ? $viewModel : null;

$heroData = $layout->heroData ?? $shellViewModel?->heroData;
$globalUi = $layout->globalUi ?? $shellViewModel?->globalUi;
$currentPage = $layout->currentPage ?? $shellViewModel?->currentPage ?? '';
$includeNav = $layout->includeNav ?? $shellViewModel?->includeNav ?? false;
$isLoggedIn = $layout->isLoggedIn ?? $globalUi?->isLoggedIn ?? false;

$viewModelVars = $shellViewModel !== null ? get_object_vars($shellViewModel) : [];

$gradientSectionCandidate = $layout->gradientSection ?? ($viewModelVars['gradientSection'] ?? null);
$gradientSection = $gradientSectionCandidate instanceof GradientSectionData
    ? $gradientSectionCandidate
    : null;

$introSplitSectionCandidate = $layout->introSplitSection ?? ($viewModelVars['introSplitSection'] ?? null);
$introSplitSection = $introSplitSectionCandidate instanceof IntroSplitSectionData
    ? $introSplitSectionCandidate
    : null;
?>

<?php ViewRenderer::render(__DIR__ . '/header.php', [
    'currentPage' => $currentPage,
    'includeNav' => $includeNav,
    'isLoggedIn' => $isLoggedIn,
]); ?>

<main class="<?= htmlspecialchars($layout->mainClass) ?>"<?php if ($layout->mainId !== null): ?> id="<?= htmlspecialchars($layout->mainId) ?>"<?php endif; ?><?php if ($layout->mainFocusable): ?> tabindex="-1"<?php endif; ?>>
    <?php if ($layout->includeHero && $heroData !== null && $globalUi !== null): ?>
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
        <?php ViewRenderer::render(
            $contentTemplate->path,
            $contentTemplate->locals + ($viewModel !== null ? ['viewModel' => $viewModel] : []),
        ); ?>
    <?php endforeach; ?>
</main>

<?php ViewRenderer::render(__DIR__ . '/footer.php', [
    'globalUi' => $globalUi,
    'currentPage' => $currentPage,
    'extraScripts' => $layout->extraScripts,
]); ?>

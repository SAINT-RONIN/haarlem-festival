<?php
/**
 * Shared page shell for public pages that use BaseViewModel.
 *
 * Required:
 * @var \App\ViewModels\BaseViewModel $viewModel
 *
 * Optional:
 * @var string|array<int, string> $pageContentPartials Absolute partial path(s) rendered inside <main>
 * @var bool $includeHero Whether to render the shared hero partial (default: true)
 * @var bool $includeEventSections Whether to render shared event sections (gradient + intro)
 * @var string $eventIntroSectionId Optional section id passed to intro split section
 * @var string|null $eventIntroImageClass Optional image class passed to intro split section
 * @var string $mainClass Main element CSS classes
 */

use App\ViewModels\BaseViewModel;

if (!isset($viewModel) || !$viewModel instanceof BaseViewModel) {
    throw new \InvalidArgumentException('The shared shell expects $viewModel to be an instance of BaseViewModel.');
}

$globalData = $viewModel->getGlobalData();
$cms = $globalData['cms'];

// Keep compatibility with pages that still provide extra CMS blocks (for example HomePageViewModel::$cmsContent).
if (property_exists($viewModel, 'cmsContent') && is_array($viewModel->cmsContent) && $viewModel->cmsContent !== []) {
    $cms = array_merge($viewModel->cmsContent, $cms);
}

$currentPage = $globalData['currentPage'];
$includeNav = $globalData['includeNav'];
$isLoggedIn = $viewModel->globalUi->isLoggedIn;
$includeHero = $includeHero ?? true;
$includeEventSections = $includeEventSections ?? false;
$eventIntroSectionId = $eventIntroSectionId ?? null;
$eventIntroImageClass = $eventIntroImageClass ?? null;
$mainClass = $mainClass ?? 'w-full bg-sand inline-flex flex-col justify-start items-center';

$contentPartials = [];
if (isset($pageContentPartials)) {
    $contentPartials = is_array($pageContentPartials) ? $pageContentPartials : [$pageContentPartials];
}
?>

<?php require __DIR__ . '/header.php'; ?>

<main class="<?= htmlspecialchars($mainClass) ?>">
    <?php if ($includeHero): ?>
        <?php require __DIR__ . '/hero.php'; ?>
    <?php endif; ?>

    <?php if ($includeEventSections): ?>
        <?php require __DIR__ . '/sections/gradient-section.php'; ?>

        <?php if ($eventIntroSectionId !== null): ?>
            <?php $sectionId = $eventIntroSectionId; ?>
        <?php endif; ?>
        <?php if ($eventIntroImageClass !== null): ?>
            <?php $introSplitImageClass = $eventIntroImageClass; ?>
        <?php endif; ?>
        <?php require __DIR__ . '/sections/intro-split-section.php'; ?>
    <?php endif; ?>

    <?php foreach ($contentPartials as $contentPartialPath): ?>
        <?php require $contentPartialPath; ?>
    <?php endforeach; ?>
</main>

<?php require __DIR__ . '/footer.php'; ?>

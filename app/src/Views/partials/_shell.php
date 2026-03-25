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

/** @var BaseViewModel $viewModel — guaranteed by the controller's renderPage() method */

$cms = $viewModel->cms;
$currentPage = $viewModel->currentPage;
$includeNav = $viewModel->includeNav;
$isLoggedIn = $viewModel->globalUi->isLoggedIn;
$includeHero = $includeHero ?? true;
$includeEventSections = $includeEventSections ?? false;
$eventIntroSectionId = $eventIntroSectionId ?? null;
$eventIntroImageClass = $eventIntroImageClass ?? null;
$mainClass = $mainClass ?? 'w-full bg-sand inline-flex flex-col justify-start items-center';

/** @var string[] $contentPartials — always an array, pre-normalized by the controller */
$contentPartials = (array)($pageContentPartials ?? []);
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

<?php
/**
 * @var \App\ViewModels\Cms\CmsPageInfoViewModel $page
 * @var \App\ViewModels\Cms\CmsSectionDisplayViewModel[] $sections
 * @var \App\ViewModels\Cms\CmsJazzLineupManagerViewModel|null $jazzLineupManager
 * @var array{HEADING: int, TEXT: int, HTML: int, BUTTON_TEXT: int} $contentLimits
 * @var \App\ViewModels\Cms\CmsImageLimitsViewModel $imageLimits
 * @var string|null $successMessage
 * @var string|null $errorMessage
 * @var string $userName
 */

$userName ??= 'Administrator';
?>
<script>
    const contentLimits = <?= json_encode($contentLimits) ?>;
    const imageLimits = <?= json_encode($imageLimits) ?>;
    const pageId = <?= $page->id ?>;
    const pageSlug = <?= json_encode($page->slug) ?>;
    const csrfToken = <?= json_encode($csrfToken ?? '') ?>;
</script>
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="/cms/pages"
                   class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Edit: <?= htmlspecialchars($page->title) ?></h1>
                    <p class="text-sm text-gray-500">Slug: /<?= htmlspecialchars($page->slug) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= htmlspecialchars($previewUrl ?? ('/' . ($page->slug === 'home' ? '' : $page->slug))) ?>"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Preview
                </a>
                <button type="submit" form="page-edit-form"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Changes
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <?php if ($successMessage): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form id="page-edit-form" action="/cms/pages/<?= $page->id ?>/<?= htmlspecialchars($page->slug) ?>/edit"
                  method="POST" class="space-y-6">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <?php foreach ($sections as $section): ?>
                    <?php if ($section->isEditable): ?>
                        <?php \App\View\ViewRenderer::render(__DIR__ . '/../edit-section-accordion.php', [
                            'section'           => $section,
                            'imageLimits'       => $imageLimits,
                            'jazzLineupManager' => $jazzLineupManager ?? null,
                        ]); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </form>
        </div>

<?php
/**
 * Shared CMS artist form partial — used by both create and edit pages.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsArtistFormViewModel $viewModel
 */

$isEditMode = $viewModel->artistId !== null;
$subtitleText = $isEditMode
    ? 'Update the artist profile, Jazz card, and Jazz detail-page content.'
    : 'Create a new artist profile and configure the Jazz page content.';

/**
 * @param array<string, string> $errors
 */
$inputClass = static function (array $errors, string $field): string {
    return 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border '
        . (isset($errors[$field]) ? 'border-red-500' : '');
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($viewModel->pageTitle) ?> - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($viewModel->pageTitle) ?></h1>
                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($subtitleText) ?></p>
                </div>
                <a href="<?= htmlspecialchars($viewModel->backUrl) ?>" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-800">
                    <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                    <?= $viewModel->returnTo !== '' ? 'Back to Jazz Lineup' : 'Back to Artists' ?>
                </a>
            </div>
        </header>

        <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">
            <?php if ($viewModel->returnTo !== ''): ?>
                <input type="hidden" name="returnTo" value="<?= htmlspecialchars($viewModel->returnTo) ?>">
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Profile</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($viewModel->name) ?>"
                               class="<?= $inputClass($viewModel->errors, 'name') ?>">
                        <?php if (isset($viewModel->errors['name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="style" class="block text-sm font-medium text-gray-700 mb-1">
                            Style <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="style" id="style" value="<?= htmlspecialchars($viewModel->style) ?>"
                               class="<?= $inputClass($viewModel->errors, 'style') ?>">
                        <?php if (isset($viewModel->errors['style'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['style']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="imageAssetId" class="block text-sm font-medium text-gray-700 mb-1">Card Image Asset ID</label>
                        <input type="number" name="imageAssetId" id="imageAssetId"
                               value="<?= $viewModel->imageAssetId !== null ? htmlspecialchars((string)$viewModel->imageAssetId) : '' ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <p class="mt-1 text-xs text-gray-500">Media Asset ID from the media library</p>
                    </div>

                    <div>
                        <label for="cardSortOrder" class="block text-sm font-medium text-gray-700 mb-1">Jazz Card Sort Order</label>
                        <input type="number" name="cardSortOrder" id="cardSortOrder"
                               value="<?= htmlspecialchars((string)$viewModel->cardSortOrder) ?>"
                               class="<?= $inputClass($viewModel->errors, 'cardSortOrder') ?>">
                        <?php if (isset($viewModel->errors['cardSortOrder'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['cardSortOrder']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="showOnJazzOverview" id="showOnJazzOverview" value="1"
                               <?= $viewModel->showOnJazzOverview ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="showOnJazzOverview" class="text-sm font-medium text-gray-700">Show on Jazz overview</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="isActive" id="isActive" value="1"
                               <?= $viewModel->isActive ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Jazz Overview Card</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6">
                    <div>
                        <label for="cardDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Card Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="cardDescription" id="cardDescription" rows="4"
                                  class="<?= $inputClass($viewModel->errors, 'cardDescription') ?>"><?= htmlspecialchars($viewModel->cardDescription) ?></textarea>
                        <?php if (isset($viewModel->errors['cardDescription'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['cardDescription']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Hero And Overview</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="heroSubtitle" class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                        <input type="text" name="heroSubtitle" id="heroSubtitle" value="<?= htmlspecialchars($viewModel->heroSubtitle) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="heroImagePath" class="block text-sm font-medium text-gray-700 mb-1">Hero Image Path</label>
                        <input type="text" name="heroImagePath" id="heroImagePath" value="<?= htmlspecialchars($viewModel->heroImagePath) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <p class="mt-1 text-xs text-gray-500">Use an asset path such as `/assets/Image/Jazz/example.png`</p>
                    </div>

                    <div>
                        <label for="originText" class="block text-sm font-medium text-gray-700 mb-1">Origin Text</label>
                        <input type="text" name="originText" id="originText" value="<?= htmlspecialchars($viewModel->originText) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="formedText" class="block text-sm font-medium text-gray-700 mb-1">Formed Text</label>
                        <input type="text" name="formedText" id="formedText" value="<?= htmlspecialchars($viewModel->formedText) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div class="md:col-span-2">
                        <label for="overviewLead" class="block text-sm font-medium text-gray-700 mb-1">Overview Lead</label>
                        <textarea name="overviewLead" id="overviewLead" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->overviewLead) ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="bioHtml" class="block text-sm font-medium text-gray-700 mb-1">
                            Main Bio / Overview Body <span class="text-red-500">*</span>
                        </label>
                        <textarea name="bioHtml" id="bioHtml" rows="8" data-tinymce
                                  class="<?= $inputClass($viewModel->errors, 'bioHtml') ?>"><?= htmlspecialchars($viewModel->bioHtml) ?></textarea>
                        <?php if (isset($viewModel->errors['bioHtml'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['bioHtml']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="overviewBodySecondary" class="block text-sm font-medium text-gray-700 mb-1">Overview Secondary Body</label>
                        <textarea name="overviewBodySecondary" id="overviewBodySecondary" rows="6" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->overviewBodySecondary) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Sections</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="lineupHeading" class="block text-sm font-medium text-gray-700 mb-1">Lineup Heading</label>
                        <input type="text" name="lineupHeading" id="lineupHeading" value="<?= htmlspecialchars($viewModel->lineupHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="highlightsHeading" class="block text-sm font-medium text-gray-700 mb-1">Highlights Heading</label>
                        <input type="text" name="highlightsHeading" id="highlightsHeading" value="<?= htmlspecialchars($viewModel->highlightsHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="photoGalleryHeading" class="block text-sm font-medium text-gray-700 mb-1">Photo Gallery Heading</label>
                        <input type="text" name="photoGalleryHeading" id="photoGalleryHeading" value="<?= htmlspecialchars($viewModel->photoGalleryHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="albumsHeading" class="block text-sm font-medium text-gray-700 mb-1">Albums Heading</label>
                        <input type="text" name="albumsHeading" id="albumsHeading" value="<?= htmlspecialchars($viewModel->albumsHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="listenHeading" class="block text-sm font-medium text-gray-700 mb-1">Listen Heading</label>
                        <input type="text" name="listenHeading" id="listenHeading" value="<?= htmlspecialchars($viewModel->listenHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="listenSubheading" class="block text-sm font-medium text-gray-700 mb-1">Listen Subheading</label>
                        <input type="text" name="listenSubheading" id="listenSubheading" value="<?= htmlspecialchars($viewModel->listenSubheading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="liveCtaHeading" class="block text-sm font-medium text-gray-700 mb-1">Live CTA Heading</label>
                        <input type="text" name="liveCtaHeading" id="liveCtaHeading" value="<?= htmlspecialchars($viewModel->liveCtaHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div>
                        <label for="performancesHeading" class="block text-sm font-medium text-gray-700 mb-1">Performances Heading</label>
                        <input type="text" name="performancesHeading" id="performancesHeading" value="<?= htmlspecialchars($viewModel->performancesHeading) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <div class="md:col-span-2">
                        <label for="photoGalleryDescription" class="block text-sm font-medium text-gray-700 mb-1">Photo Gallery Description</label>
                        <textarea name="photoGalleryDescription" id="photoGalleryDescription" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->photoGalleryDescription) ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="albumsDescription" class="block text-sm font-medium text-gray-700 mb-1">Albums Description</label>
                        <textarea name="albumsDescription" id="albumsDescription" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->albumsDescription) ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="listenDescription" class="block text-sm font-medium text-gray-700 mb-1">Listen Description</label>
                        <textarea name="listenDescription" id="listenDescription" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->listenDescription) ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="liveCtaDescription" class="block text-sm font-medium text-gray-700 mb-1">Live CTA Description</label>
                        <textarea name="liveCtaDescription" id="liveCtaDescription" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->liveCtaDescription) ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="performancesDescription" class="block text-sm font-medium text-gray-700 mb-1">Performances Description</label>
                        <textarea name="performancesDescription" id="performancesDescription" rows="4" data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->performancesDescription) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 flex justify-end gap-3">
                <a href="/cms/artists"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Save Artist
                </button>
            </div>
        </form>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
<script>
    tinymce.init({
        selector: 'textarea[data-tinymce]',
        height: 220,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
        setup: function(editor) {
            editor.on('change', function() { editor.save(); });
        }
    });
    lucide.createIcons();
</script>
</body>
</html>

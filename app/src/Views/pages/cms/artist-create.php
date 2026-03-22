<?php
/**
 * CMS Create Artist form page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsArtistFormViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Artist - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($viewModel->pageTitle) ?></h1>
                    <p class="text-gray-600 mt-1">Fill in the details to create a new artist listing.</p>
                </div>
                <a href="/cms/artists" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-800">
                    <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                    Back to Artists
                </a>
            </div>
        </header>

        <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">

            <!-- Artist Details -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Artist Details</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="<?= htmlspecialchars($viewModel->name) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['name']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Style -->
                    <div>
                        <label for="style" class="block text-sm font-medium text-gray-700 mb-1">
                            Style <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="style"
                               id="style"
                               value="<?= htmlspecialchars($viewModel->style) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['style']) ? 'border-red-500' : '' ?>">
                        <p class="mt-1 text-xs text-gray-500">e.g., Jazz Quartet, Blues Singer</p>
                        <?php if (isset($viewModel->errors['style'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['style']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Image Asset ID -->
                    <div>
                        <label for="imageAssetId" class="block text-sm font-medium text-gray-700 mb-1">
                            Image Asset ID
                        </label>
                        <input type="number"
                               name="imageAssetId"
                               id="imageAssetId"
                               value="<?= $viewModel->imageAssetId !== null ? htmlspecialchars((string) $viewModel->imageAssetId) : '' ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <p class="mt-1 text-xs text-gray-500">Media Asset ID from the media library</p>
                    </div>

                    <!-- Is Active -->
                    <div class="flex items-center gap-2">
                        <input type="checkbox"
                               name="isActive"
                               id="isActive"
                               value="1"
                               checked
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Bio</h2>
                </div>
                <div class="p-6">
                    <label for="bioHtml" class="block text-sm font-medium text-gray-700 mb-1">
                        Bio <span class="text-red-500">*</span>
                    </label>
                    <textarea name="bioHtml"
                              id="bioHtml"
                              rows="8"
                              data-tinymce
                              class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border <?= isset($viewModel->errors['bioHtml']) ? 'border-red-500' : '' ?>"><?= htmlspecialchars($viewModel->bioHtml) ?></textarea>
                    <?php if (isset($viewModel->errors['bioHtml'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['bioHtml']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Actions -->
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
        height: 300,
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

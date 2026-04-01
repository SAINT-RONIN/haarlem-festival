<?php
/**
 * CMS Create Restaurant form page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsRestaurantFormViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Restaurant - Haarlem CMS</title>
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
                    <p class="text-gray-600 mt-1">Fill in the details to create a new restaurant event.</p>
                </div>
                <a href="/cms/restaurants" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-800">
                    <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                    Back to Restaurants
                </a>
            </div>
        </header>

        <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">

            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Info</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="<?= htmlspecialchars($viewModel->title) ?>"
                               placeholder="e.g. Ratatouille - Festival Dinner"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['title']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['title'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['title']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               value="<?= htmlspecialchars($viewModel->slug) ?>"
                               placeholder="e.g. ratatouille"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['slug']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['slug'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['slug']) ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-xs text-gray-500">Used in the URL: /restaurant/{slug}</p>
                    </div>

                    <!-- Short Description -->
                    <div class="md:col-span-2">
                        <label for="shortDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Short Description
                        </label>
                        <input type="text"
                               name="shortDescription"
                               id="shortDescription"
                               value="<?= htmlspecialchars($viewModel->shortDescription) ?>"
                               placeholder="e.g. French, fish and seafood, European cuisine"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Featured Image Asset ID -->
                    <div>
                        <label for="featuredImageAssetId" class="block text-sm font-medium text-gray-700 mb-1">
                            Featured Image Asset ID
                        </label>
                        <input type="number"
                               name="featuredImageAssetId"
                               id="featuredImageAssetId"
                               value="<?= $viewModel->featuredImageAssetId !== null ? htmlspecialchars((string) $viewModel->featuredImageAssetId) : '' ?>"
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

            <!-- Long Description -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                </div>
                <div class="p-6">
                    <label for="longDescriptionHtml" class="block text-sm font-medium text-gray-700 mb-1">
                        Long Description
                    </label>
                    <textarea name="longDescriptionHtml"
                              id="longDescriptionHtml"
                              rows="8"
                              data-tinymce
                              class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->longDescriptionHtml) ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-lg shadow p-4 flex justify-end gap-3">
                <a href="/cms/restaurants"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Save Restaurant
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

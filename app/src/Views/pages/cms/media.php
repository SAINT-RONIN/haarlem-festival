<?php
/**
 * CMS Media Library page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsMediaLibraryViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Library - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
                    <p class="text-gray-600 mt-1">Manage uploaded images</p>
                </div>
                <button
                    type="button"
                    data-action="triggerUpload"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="upload" class="w-5 h-5 mr-2"></i>
                    Upload Image
                </button>
                <input type="file" id="media-file-input" accept="image/*" class="hidden">
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

        <!-- Upload Drop Zone -->
        <div id="drop-zone"
             class="mb-6 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors cursor-pointer">
            <i data-lucide="image-plus" class="w-10 h-10 mx-auto text-gray-400 mb-3"></i>
            <p class="text-gray-600 font-medium">Drag and drop an image here, or click to browse</p>
            <p class="text-sm text-gray-400 mt-1">
                Allowed: <?= htmlspecialchars(implode(', ', $viewModel->imageLimits['allowedExtensions'] ?? [])) ?>
                &middot; Max size: <?= htmlspecialchars($viewModel->imageLimits['maxFileSizeFormatted'] ?? '') ?>
                &middot; Max dimensions: <?= htmlspecialchars((string) ($viewModel->imageLimits['maxWidth'] ?? '')) ?>&times;<?= htmlspecialchars((string) ($viewModel->imageLimits['maxHeight'] ?? '')) ?>px
            </p>
        </div>

        <!-- Stats Bar -->
        <div class="mb-4">
            <p id="asset-count-label" class="text-sm text-gray-500">
                <span id="asset-count"><?= count($viewModel->assets) ?></span> image(s) in library
            </p>
        </div>

        <!-- Image Grid -->
        <?php if (empty($viewModel->assets)): ?>
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <div class="col-span-full py-16 text-center text-gray-400">
                    <i data-lucide="image-off" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-base font-medium text-gray-500">No images uploaded yet</p>
                    <p class="text-sm mt-1">Upload your first image using the button above.</p>
                </div>
            </div>
        <?php else: ?>
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <?php foreach ($viewModel->assets as $asset): ?>
                    <?php /** @var \App\ViewModels\Cms\CmsMediaListItemViewModel $asset */ ?>
                    <div id="asset-<?= (int) $asset->mediaAssetId ?>" class="bg-white rounded-lg shadow overflow-hidden group relative">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            <img
                                src="<?= htmlspecialchars($asset->filePath) ?>"
                                alt="<?= htmlspecialchars($asset->altText) ?>"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-2">
                            <p class="text-xs text-gray-700 truncate font-medium" title="<?= htmlspecialchars($asset->originalFileName) ?>">
                                <?= htmlspecialchars($asset->originalFileName) ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                <?= htmlspecialchars($asset->fileSize) ?> &middot; <?= htmlspecialchars($asset->createdAt) ?>
                            </p>
                        </div>
                        <button
                            data-delete-asset="<?= (int) $asset->mediaAssetId ?>"
                            class="absolute top-2 right-2 w-7 h-7 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Delete image">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
window.mediaConfig = {
    csrfToken: '<?= htmlspecialchars($viewModel->csrfToken) ?>',
    imageLimits: <?= json_encode($viewModel->imageLimits) ?>
};
</script>
<script src="/assets/js/cms/cms-common.js"></script>
<script src="/assets/js/cms/media.js"></script>
</body>
</html>

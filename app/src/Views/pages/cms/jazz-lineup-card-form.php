<?php
/**
 * Lightweight Jazz lineup card form used from the Jazz page editor flow.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsJazzLineupCardFormViewModel $viewModel
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">
    <link rel="icon" href="/assets/Icons/Logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/tokens.css">
    <link rel="stylesheet" href="/assets/css/cms.css">
</head>
<body class="bg-gray-50 min-h-screen cms-body">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($viewModel->pageTitle) ?></h1>
                    <p class="text-gray-600 mt-1">This form only manages the discover-lineup card fields.</p>
                </div>
                <a href="<?= htmlspecialchars($viewModel->backUrl) ?>" class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-800">
                    <i data-lucide="arrow-left" class="w-4 h-4" aria-hidden="true"></i>
                    Back to Jazz Lineup
                </a>
            </div>
        </header>

        <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>" class="space-y-6" id="jazzLineupCardForm">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">
            <input type="hidden" name="returnTo" value="<?= htmlspecialchars($viewModel->returnTo) ?>">

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Card Details</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <?php if ($viewModel->artistId !== null): ?>
                        <!-- Edit mode: show artist name as read-only -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Artist</label>
                            <input type="text" value="<?= htmlspecialchars($viewModel->name) ?>" readonly
                                   class="block w-full rounded-md border-gray-200 bg-gray-100 shadow-sm py-2 px-3 border text-gray-600 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Style</label>
                            <input type="text" value="<?= htmlspecialchars($viewModel->style) ?>" readonly
                                   class="block w-full rounded-md border-gray-200 bg-gray-100 shadow-sm py-2 px-3 border text-gray-600 cursor-not-allowed">
                        </div>
                        <input type="hidden" name="name" value="<?= htmlspecialchars($viewModel->name) ?>">
                        <input type="hidden" name="style" value="<?= htmlspecialchars($viewModel->style) ?>">
                    <?php else: ?>
                        <!-- Create mode: artist selector dropdown -->
                        <div class="md:col-span-2">
                            <label for="artistSelect" class="block text-sm font-medium text-gray-700 mb-1">
                                Artist <span class="text-red-500">*</span>
                            </label>
                            <select id="artistSelect"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                                <option value="">-- Select an artist --</option>
                                <?php foreach ($viewModel->artists as $artist): ?>
                                    <option value="<?= $artist->artistId ?>"
                                        data-name="<?= htmlspecialchars($artist->name) ?>"
                                        data-style="<?= htmlspecialchars($artist->style) ?>"
                                        data-description="<?= htmlspecialchars($artist->description) ?>"
                                        data-image-asset-id="<?= $artist->imageAssetId !== null ? (int) $artist->imageAssetId : '' ?>"
                                        data-image-url="<?= htmlspecialchars($artist->imageUrl) ?>">
                                        <?= htmlspecialchars($artist->name) ?> — <?= htmlspecialchars($artist->style) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="mt-2">
                                <a href="/cms/artists/create" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    Create new artist
                                </a>
                            </div>
                            <?php if (isset($viewModel->errors['name'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="name" id="name" value="">
                        <input type="hidden" name="style" id="style" value="">
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lineup Image</label>
                        <input type="hidden" name="imageAssetId" id="imageAssetId"
                               value="<?= $viewModel->imageAssetId !== null ? (int) $viewModel->imageAssetId : '' ?>">
                        <div class="flex items-start gap-4">
                            <div id="jazzCardImagePreview" class="flex-shrink-0">
                                <?php if ($viewModel->imageUrl !== ''): ?>
                                    <img src="<?= htmlspecialchars($viewModel->imageUrl) ?>"
                                         alt="Lineup image"
                                         class="w-32 h-24 rounded-lg border border-gray-200 object-cover">
                                <?php else: ?>
                                    <div class="w-32 h-24 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                                        <i data-lucide="image" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button type="button" data-action="openJazzCardImagePicker"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                    <i data-lucide="image" class="w-4 h-4"></i>
                                    Choose from Library
                                </button>
                                <?php if ($viewModel->imageAssetId !== null): ?>
                                    <button type="button" data-action="clearJazzCardImage"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        Remove Image
                                    </button>
                                <?php endif; ?>
                                <p class="text-xs text-gray-500">Select an image from the media library.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="cardSortOrder" class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" name="cardSortOrder" id="cardSortOrder" value="<?= htmlspecialchars((string) $viewModel->cardSortOrder) ?>"
                               class="<?= $inputClass($viewModel->errors, 'cardSortOrder') ?>">
                        <?php if (isset($viewModel->errors['cardSortOrder'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['cardSortOrder']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="cardDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="cardDescription" id="cardDescription" rows="5"
                                  class="<?= $inputClass($viewModel->errors, 'cardDescription') ?>"><?= htmlspecialchars($viewModel->cardDescription) ?></textarea>
                        <?php if (isset($viewModel->errors['cardDescription'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['cardDescription']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="isActive" id="isActive" value="1"
                               <?= $viewModel->isActive ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="<?= htmlspecialchars($viewModel->backUrl) ?>"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Card
                </button>
            </div>
        </form>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
</body>
</html>

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

        <form method="POST" action="<?= htmlspecialchars($viewModel->formAction) ?>" class="space-y-6">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->csrfToken) ?>">
            <input type="hidden" name="returnTo" value="<?= htmlspecialchars($viewModel->returnTo) ?>">

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Card Details</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Title <span class="text-red-500">*</span>
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
                        <label for="imageAssetId" class="block text-sm font-medium text-gray-700 mb-1">Image Asset ID</label>
                        <input type="number" name="imageAssetId" id="imageAssetId"
                               value="<?= $viewModel->imageAssetId !== null ? htmlspecialchars((string)$viewModel->imageAssetId) : '' ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <p class="mt-1 text-xs text-gray-500">Optional MediaAsset ID for the lineup image.</p>
                    </div>

                    <div>
                        <label for="cardSortOrder" class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" name="cardSortOrder" id="cardSortOrder" value="<?= htmlspecialchars((string)$viewModel->cardSortOrder) ?>"
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

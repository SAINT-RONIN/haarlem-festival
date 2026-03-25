<?php
/**
 * Shared CMS restaurant form partial — used by both create and edit pages.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsRestaurantFormViewModel $viewModel
 */

$isEditMode = $viewModel->restaurantId !== null;
$subtitleText = $isEditMode
    ? 'Update the details for this restaurant listing.'
    : 'Fill in the details to create a new restaurant listing.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($viewModel->pageTitle) ?> - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($viewModel->pageTitle) ?></h1>
                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($subtitleText) ?></p>
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

                    <!-- Address Line -->
                    <div>
                        <label for="addressLine" class="block text-sm font-medium text-gray-700 mb-1">
                            Address Line <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="addressLine"
                               id="addressLine"
                               value="<?= htmlspecialchars($viewModel->addressLine) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['addressLine']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['addressLine'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['addressLine']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="city"
                               id="city"
                               value="<?= htmlspecialchars($viewModel->city) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['city']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['city'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['city']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Cuisine Type -->
                    <div>
                        <label for="cuisineType" class="block text-sm font-medium text-gray-700 mb-1">
                            Cuisine Type <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="cuisineType"
                               id="cuisineType"
                               value="<?= htmlspecialchars($viewModel->cuisineType) ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border <?= isset($viewModel->errors['cuisineType']) ? 'border-red-500' : '' ?>">
                        <?php if (isset($viewModel->errors['cuisineType'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['cuisineType']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Stars -->
                    <div>
                        <label for="stars" class="block text-sm font-medium text-gray-700 mb-1">Stars</label>
                        <select name="stars"
                                id="stars"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">-- Select --</option>
                            <?php foreach ([0, 1, 2, 3, 4, 5] as $star): ?>
                                <option value="<?= $star ?>" <?= $viewModel->stars === $star ? 'selected' : '' ?>>
                                    <?= $star ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                               <?= $viewModel->isActive ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <label for="isActive" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                </div>
                <div class="p-6">
                    <label for="descriptionHtml" class="block text-sm font-medium text-gray-700 mb-1">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="descriptionHtml"
                              id="descriptionHtml"
                              rows="8"
                              data-tinymce
                              class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border <?= isset($viewModel->errors['descriptionHtml']) ? 'border-red-500' : '' ?>"><?= htmlspecialchars($viewModel->descriptionHtml) ?></textarea>
                    <?php if (isset($viewModel->errors['descriptionHtml'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($viewModel->errors['descriptionHtml']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Info -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Detail Info</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               value="<?= htmlspecialchars($viewModel->phone ?? '') ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="<?= htmlspecialchars($viewModel->email ?? '') ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url"
                               name="website"
                               id="website"
                               value="<?= htmlspecialchars($viewModel->website ?? '') ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Michelin Stars -->
                    <div>
                        <label for="michelinStars" class="block text-sm font-medium text-gray-700 mb-1">Michelin Stars</label>
                        <select name="michelinStars"
                                id="michelinStars"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">-- Select --</option>
                            <?php foreach ([0, 1, 2, 3] as $michelin): ?>
                                <option value="<?= $michelin ?>" <?= $viewModel->michelinStars === $michelin ? 'selected' : '' ?>>
                                    <?= $michelin ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Seats per Session -->
                    <div>
                        <label for="seatsPerSession" class="block text-sm font-medium text-gray-700 mb-1">Seats per Session</label>
                        <input type="number"
                               name="seatsPerSession"
                               id="seatsPerSession"
                               value="<?= $viewModel->seatsPerSession !== null ? htmlspecialchars((string) $viewModel->seatsPerSession) : '' ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Duration Minutes -->
                    <div>
                        <label for="durationMinutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number"
                               name="durationMinutes"
                               id="durationMinutes"
                               value="<?= $viewModel->durationMinutes !== null ? htmlspecialchars((string) $viewModel->durationMinutes) : '' ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>
                </div>
            </div>

            <!-- About -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">About</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6">
                    <!-- About Text -->
                    <div>
                        <label for="aboutText" class="block text-sm font-medium text-gray-700 mb-1">About Text</label>
                        <textarea name="aboutText"
                                  id="aboutText"
                                  rows="8"
                                  data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->aboutText ?? '') ?></textarea>
                    </div>

                    <!-- Chef Name -->
                    <div>
                        <label for="chefName" class="block text-sm font-medium text-gray-700 mb-1">Chef Name</label>
                        <input type="text"
                               name="chefName"
                               id="chefName"
                               value="<?= htmlspecialchars($viewModel->chefName ?? '') ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>

                    <!-- Chef Text -->
                    <div>
                        <label for="chefText" class="block text-sm font-medium text-gray-700 mb-1">Chef Text</label>
                        <textarea name="chefText"
                                  id="chefText"
                                  rows="8"
                                  data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->chefText ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Menu & Location -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Menu &amp; Location</h2>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6">
                    <!-- Menu Description -->
                    <div>
                        <label for="menuDescription" class="block text-sm font-medium text-gray-700 mb-1">Menu Description</label>
                        <textarea name="menuDescription"
                                  id="menuDescription"
                                  rows="8"
                                  data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->menuDescription ?? '') ?></textarea>
                    </div>

                    <!-- Location Description -->
                    <div>
                        <label for="locationDescription" class="block text-sm font-medium text-gray-700 mb-1">Location Description</label>
                        <textarea name="locationDescription"
                                  id="locationDescription"
                                  rows="8"
                                  data-tinymce
                                  class="block w-full rounded-md border-gray-300 shadow-sm py-2 px-3 border"><?= htmlspecialchars($viewModel->locationDescription ?? '') ?></textarea>
                    </div>

                    <!-- Map Embed URL -->
                    <div>
                        <label for="mapEmbedUrl" class="block text-sm font-medium text-gray-700 mb-1">Map Embed URL</label>
                        <input type="text"
                               name="mapEmbedUrl"
                               id="mapEmbedUrl"
                               value="<?= htmlspecialchars($viewModel->mapEmbedUrl ?? '') ?>"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>
                </div>
            </div>

            <!-- Special Requests -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Special Requests</h2>
                </div>
                <div class="p-6">
                    <label for="specialRequestsNote" class="block text-sm font-medium text-gray-700 mb-1">Special Requests Note</label>
                    <textarea name="specialRequestsNote"
                              id="specialRequestsNote"
                              rows="4"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border"><?= htmlspecialchars($viewModel->specialRequestsNote ?? '') ?></textarea>
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

<?php
/**
 * CMS Artists list page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsArtistsListViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artists - Haarlem CMS</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Artists Management</h1>
                    <p class="text-gray-600 mt-1">View and manage artist listings</p>
                </div>
                <a href="/cms/artists/create"
                   class="flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
                    Create Artist
                </a>
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

        <!-- Search -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="/cms/artists" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="<?= htmlspecialchars($viewModel->searchQuery) ?>"
                           placeholder="Artist name or style..."
                           class="block w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Apply Filters
                </button>
                <?php if ($viewModel->searchQuery !== ''): ?>
                    <a href="/cms/artists" class="px-4 py-2 text-gray-600 hover:text-gray-800">Clear Filters</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Artists Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">All Artists</h2>
                    <p class="text-sm text-gray-500"><?= count($viewModel->items) ?> artist(s) found</p>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Style
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Created
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->items)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <p>No artists found.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($viewModel->items as $item): ?>
                        <?php /** @var \App\ViewModels\Cms\CmsArtistListItemViewModel $item */ ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($item->name) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($item->style) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($item->isActive): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                        Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($item->createdAt) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="/cms/artists/<?= htmlspecialchars((string) $item->artistId) ?>/edit"
                                       class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form method="POST"
                                          action="/cms/artists/<?= htmlspecialchars((string) $item->artistId) ?>/delete"
                                          data-confirm="Are you sure you want to deactivate this artist?">
                                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($viewModel->deleteCsrfToken) ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Deactivate</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
</body>
</html>

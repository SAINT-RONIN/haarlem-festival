<?php
// ...existing code...
?>

<section aria-labelledby="pages-table-heading" class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <h2 id="pages-table-heading" class="text-sm font-medium text-gray-700">Pages</h2>
        <div class="text-sm text-gray-700" aria-live="polite">
            <?= count($pages) ?> <?= count($pages) === 1 ? 'page' : 'pages' ?>
        </div>
        <button type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
            <i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i>
            New Page
        </button>
    </div>

    <table class="w-full">
        <caption class="sr-only">List of CMS pages</caption>
        <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Page
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Slug
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Last Updated
            </th>
            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Actions
            </th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if (empty($pages)): ?>
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <i data-lucide="file-x" class="w-12 h-12 mx-auto mb-4 text-gray-300" aria-hidden="true"></i>
                    <p class="text-sm">No pages found matching your search.</p>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($pages as $page): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <th scope="row" class="px-6 py-4 text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0"
                                 aria-hidden="true">
                                <i data-lucide="file-text" class="w-5 h-5" aria-hidden="true"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($page['title']) ?></span>
                        </div>
                    </th>
                    <td class="px-6 py-4">
                        <code class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">/<?= htmlspecialchars($page['slug']) ?></code>
                    </td>
                    <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $page['status'] === 'Published' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                                <?= htmlspecialchars($page['status']) ?>
                            </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500"><?= htmlspecialchars($page['updatedAt']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/cms/pages/<?= $page['id'] ?>/<?= htmlspecialchars($page['slug']) ?>/edit"
                               class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                               aria-label="Edit <?= htmlspecialchars($page['title']) ?>">
                                <i data-lucide="edit" class="w-4 h-4" aria-hidden="true"></i>
                            </a>
                            <a href="/<?= htmlspecialchars($page['slug']) ?>"
                               target="_blank"
                               class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                               aria-label="View <?= htmlspecialchars($page['title']) ?>">
                                <i data-lucide="external-link" class="w-4 h-4" aria-hidden="true"></i>
                            </a>
                            <button type="button"
                                    class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    aria-label="Delete <?= htmlspecialchars($page['title']) ?>">
                                <i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>


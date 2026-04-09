<?php
/**
 * CMS Header partial - Top navigation bar for admin panel.
 *
 * @var string $currentView Current navigation state
 * @var string $searchQuery Current search query (optional)
 */
$currentView ??= 'dashboard';
$searchQuery ??= '';
$pageTitle = match ($currentView) {
    'dashboard' => 'Dashboard',
    'pages' => 'Pages',
    default => 'CMS'
};
?>
<!-- Header -->
<header class="bg-white border-b border-gray-200 px-6 py-4" role="banner">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-sm text-gray-500">
                <?php if ($currentView === 'dashboard'): ?>
                    Welcome back! Here's what's happening with your site.
                <?php elseif ($currentView === 'pages'): ?>
                    Manage and edit your website pages.
                <?php endif; ?>
            </p>
        </div>

        <?php if ($currentView === 'pages'): ?>
            <div class="flex items-center gap-4">
                <form action="/cms/pages" method="get" class="relative" role="search">
                    <label for="cms-pages-search" class="sr-only">Search pages</label>
                    <i data-lucide="search"
                       class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                       aria-hidden="true"></i>
                    <input
                            id="cms-pages-search"
                            name="search"
                            type="search"
                            placeholder="Search pages..."
                            value="<?= htmlspecialchars($searchQuery) ?>"
                            class="pl-10 pr-4 py-2 w-64 border border-gray-200 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-all"
                            aria-describedby="cms-pages-search-help">
                    <span id="cms-pages-search-help"
                          class="sr-only">Type and press Enter to filter the pages list.</span>
                </form>
            </div>
        <?php endif; ?>
    </div>
</header>


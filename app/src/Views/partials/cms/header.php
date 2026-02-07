<?php
/**
 * CMS Header partial - Top navigation bar for admin panel.
 *
 * @var string $currentView Current navigation state
 * @var string $searchQuery Current search query (optional)
 */
$currentView = $currentView ?? 'dashboard';
$searchQuery = $searchQuery ?? '';
$pageTitle = match ($currentView) {
    'dashboard' => 'Dashboard',
    'pages' => 'Pages',
    default => 'CMS'
};
?>
<!-- Header -->
<header class="bg-white border-b border-gray-200 px-6 py-4">
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
            <!-- Search (only shown on pages view) -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                       aria-hidden="true"></i>
                    <input
                            type="search"
                            placeholder="Search pages..."
                            value="<?= htmlspecialchars($searchQuery) ?>"
                            class="pl-10 pr-4 py-2 w-64 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            aria-label="Search pages">
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>


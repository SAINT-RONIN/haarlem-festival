<?php
/**
 * CMS Dashboard Content partial - Main dashboard view with quick shortcuts, recent pages, and activity.
 *
 * @var array $recentPages Recently updated pages
 * @var array $activities Recent activity feed
 */

// Default data - ALL OF THIS WILL BE DELETED ONCE WE HAVE REAL DATA FROM THE DATABASE
$recentPages = $recentPages ?? [
    ['title' => 'Home', 'status' => 'Published', 'time' => '2h ago'],
    ['title' => 'Jazz', 'status' => 'Published', 'time' => 'yesterday'],
    ['title' => 'Dance', 'status' => 'Published', 'time' => '3d ago'],
    ['title' => 'History', 'status' => 'Draft', 'time' => '6d ago'],
];

$activities = $activities ?? [
    ['icon' => 'edit', 'text' => "You updated 'Home'", 'time' => '2h ago', 'color' => 'blue'],
    ['icon' => 'file-text', 'text' => "Draft saved: 'History'", 'time' => 'yesterday', 'color' => 'amber'],
    ['icon' => 'image', 'text' => 'Media uploaded: header.jpg', 'time' => '3d ago', 'color' => 'purple'],
    ['icon' => 'user', 'text' => "User 'Editor' role updated", 'time' => '1w ago', 'color' => 'green'],
];

$shortcuts = [
    [
        'icon' => 'file-edit',
        'title' => 'Edit pages',
        'description' => 'Open the pages list and update content.',
        'color' => 'blue',
        'href' => '/cms/pages',
    ],
    [
        'icon' => 'file-check',
        'title' => 'Review drafts',
        'description' => 'See pages not published yet.',
        'color' => 'amber',
        'href' => '/cms/pages?filter=draft',
    ],
    [
        'icon' => 'image',
        'title' => 'Manage media',
        'description' => 'View and organize uploaded images/files.',
        'color' => 'purple',
        'href' => '/cms/media',
    ],
    [
        'icon' => 'users',
        'title' => 'User management',
        'description' => 'Edit roles and access.',
        'color' => 'green',
        'href' => '/cms/users',
    ],
];

$colorClasses = [
    'blue' => [
        'gradient' => 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
        'bg' => 'bg-blue-50',
        'text' => 'text-blue-600',
    ],
    'amber' => [
        'gradient' => 'from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700',
        'bg' => 'bg-amber-50',
        'text' => 'text-amber-600',
    ],
    'purple' => [
        'gradient' => 'from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700',
        'bg' => 'bg-purple-50',
        'text' => 'text-purple-600',
    ],
    'green' => [
        'gradient' => 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
        'bg' => 'bg-green-50',
        'text' => 'text-green-600',
    ],
];
?>

<section aria-labelledby="quick-shortcuts-heading" class="mb-6">
    <h2 id="quick-shortcuts-heading" class="text-lg font-semibold text-gray-900 mb-4">Quick shortcuts</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($shortcuts as $shortcut): ?>
            <?php $colors = $colorClasses[$shortcut['color']]; ?>
            <a href="<?= htmlspecialchars($shortcut['href']) ?>"
               class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5 text-left group block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $colors['gradient'] ?> flex items-center justify-center mb-4 transition-all">
                    <i data-lucide="<?= htmlspecialchars($shortcut['icon']) ?>" class="w-6 h-6 text-white"
                       aria-hidden="true"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-1"><?= htmlspecialchars($shortcut['title']) ?></h3>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($shortcut['description']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6"
             aria-labelledby="recently-updated-heading">
        <div class="flex items-center justify-between mb-4">
            <h2 id="recently-updated-heading" class="text-lg font-semibold text-gray-900">Recently updated pages</h2>
            <a href="/cms/pages"
               class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
                View all pages
                <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
            </a>
        </div>

        <ul class="space-y-3" role="list">
            <?php foreach ($recentPages as $page): ?>
                <li>
                    <article
                            class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($page['title']) ?></span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $page['status'] === 'Published' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                                    <?= htmlspecialchars($page['status']) ?>
                                </span>
                            </div>
                            <span class="text-xs text-gray-500"><?= htmlspecialchars($page['time']) ?></span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button"
                                    class="p-1.5 hover:bg-white rounded-lg transition-colors text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    aria-label="Edit <?= htmlspecialchars($page['title']) ?>">
                                <i data-lucide="edit" class="w-4 h-4" aria-hidden="true"></i>
                            </button>
                            <button type="button"
                                    class="p-1.5 hover:bg-white rounded-lg transition-colors text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    aria-label="View <?= htmlspecialchars($page['title']) ?>">
                                <i data-lucide="eye" class="w-4 h-4" aria-hidden="true"></i>
                            </button>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="bg-white border border-gray-200 rounded-xl p-6" aria-labelledby="activity-heading">
        <h2 id="activity-heading" class="text-lg font-semibold text-gray-900 mb-4">Activity</h2>

        <ul class="space-y-4" role="list">
            <?php foreach ($activities as $activity): ?>
                <?php $colors = $colorClasses[$activity['color']]; ?>
                <li class="flex gap-3">
                    <div class="w-10 h-10 rounded-lg <?= $colors['bg'] ?> <?= $colors['text'] ?> flex items-center justify-center flex-shrink-0"
                         aria-hidden="true">
                        <i data-lucide="<?= htmlspecialchars($activity['icon']) ?>" class="w-5 h-5"
                           aria-hidden="true"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900"><?= htmlspecialchars($activity['text']) ?></p>
                        <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($activity['time']) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <button type="button"
                class="w-full mt-4 pt-4 border-t border-gray-200 text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 rounded">
            View all activity
        </button>
    </section>
</div>


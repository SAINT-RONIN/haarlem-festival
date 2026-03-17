<?php
/**
 * CMS Sidebar partial - Navigation sidebar for admin panel.
 *
 * @var string $currentView Current navigation state ('dashboard' or 'pages')
 */
$currentView = $currentView ?? 'dashboard';
?>
<!-- Sidebar -->
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col" aria-label="CMS sidebar">
    <header class="p-4 border-b border-gray-200">
        <a href="/cms" class="flex items-center gap-2" aria-label="Haarlem CMS dashboard">
            <img src="/assets/Icons/Logo.svg" alt="" class="w-8 h-8" role="presentation">
            <span class="text-lg font-semibold text-royal-blue">Haarlem CMS</span>
        </a>
    </header>

    <nav class="flex-1 p-4" aria-label="CMS navigation">
        <ul class="space-y-1">
            <li>
                <a href="/cms"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'dashboard' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'dashboard' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="layout-dashboard" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/cms/pages"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'pages' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'pages' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="file-text" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Pages</span>
                </a>
            </li>
            <li>
                <a href="/cms/events"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'events' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'events' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="calendar" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Events</span>
                </a>
            </li>
            <li>
                <a href="/cms/schedule-days"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'schedule-days' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'schedule-days' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="calendar-days" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Schedule Days</span>
                </a>
            </li>
            <li>
                <a href="/cms/orders"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'orders' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'orders' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="shopping-cart" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Orders</span>
                </a>
            </li>
            <li>
                <a href="/cms/media"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'media' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'media' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="image" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Media</span>
                </a>
            </li>
            <li>
                <a href="/cms/users"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors duration-200 <?= $currentView === 'users' ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>"
                        <?= $currentView === 'users' ? 'aria-current="page"' : '' ?>>
                    <i data-lucide="users" class="w-5 h-5" aria-hidden="true"></i>
                    <span class="font-medium">Users</span>
                </a>
            </li>
        </ul>
    </nav>

    <footer class="p-4 border-t border-gray-200">
        <a href="/cms/logout"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            <i data-lucide="log-out" class="w-5 h-5" aria-hidden="true"></i>
            <span class="font-medium">Logout</span>
        </a>
    </footer>
</aside>


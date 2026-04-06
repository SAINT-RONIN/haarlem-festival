<?php
/**
 * CMS Events list page with weekly schedule overview.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsEventsListViewModel $viewModel
 */

$weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// Extract from ViewModel
$events = $viewModel->events;
$eventTypes = $viewModel->eventTypes;
$weeklySchedule = $viewModel->weeklySchedule;
$venues = $viewModel->venues;
$selectedType = $viewModel->selectedType;
$selectedDay = $viewModel->selectedDay;
$typeColors = $viewModel->typeColorMap;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Haarlem CMS</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Events Management</h1>
                    <p class="text-gray-600 mt-1">Manage events and sessions across all 7 days</p>
                </div>
                <a href="/cms/events/create"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
                    Create Event
                </a>
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="/cms/events" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <select name="type" id="type"
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <option value="">All Types</option>
                        <?php foreach ($eventTypes as $type): ?>
                            <option value="<?= $type->eventTypeId ?>"
                                    <?= $selectedType == $type->eventTypeId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="day" class="block text-sm font-medium text-gray-700 mb-1">Day of Week</label>
                    <select name="day" id="day"
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <option value="">All Days</option>
                        <?php foreach ($weekDays as $day): ?>
                            <option value="<?= $day ?>" <?= $selectedDay === $day ? 'selected' : '' ?>>
                                <?= $day ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Apply Filters
                </button>
                <?php if ($selectedType || $selectedDay): ?>
                    <a href="/cms/events" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Clear Filters
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Weekly Schedule Overview -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Weekly Schedule Overview</h2>
                <p class="text-sm text-gray-500">Sessions grouped by day of week</p>
            </div>
            <div class="grid grid-cols-7 divide-x divide-gray-200">
                <?php foreach ($weekDays as $day): ?>
                    <?php $daySessions = $weeklySchedule[$day] ?? []; ?>
                    <div class="min-h-[200px]">
                        <div class="p-3 bg-gray-50 border-b border-gray-200 sticky top-0">
                            <h3 class="font-medium text-gray-900 text-center"><?= $day ?></h3>
                            <p class="text-xs text-gray-500 text-center">
                                <?= count($daySessions) ?> session(s)
                            </p>
                        </div>
                        <div class="p-2 space-y-2 max-h-[400px] overflow-y-auto">
                            <?php if (empty($daySessions)): ?>
                                <div class="text-center py-4">
                                    <p class="text-xs text-gray-400">No events</p>
                                    <a href="/cms/events/create?day=<?= $day ?>"
                                       class="text-xs text-blue-600 hover:text-blue-800 inline-flex items-center mt-1">
                                        <i data-lucide="plus" class="w-3 h-3 mr-1"></i>
                                        Add event
                                    </a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($daySessions as $session): ?>
                                    <?php
                                    /** @var \App\ViewModels\Cms\CmsEventSessionViewModel $session */
                                    $colorClass = $typeColors[$session->eventTypeSlug] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <a href="/cms/events/<?= $session->eventId ?>/edit"
                                       class="block p-2 rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-sm transition-all text-xs">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-medium text-gray-900 line-clamp-2">
                                                <?= htmlspecialchars($session->eventTitle) ?>
                                            </span>
                                        </div>
                                        <div class="text-gray-500 mb-1">
                                            <?= $session->formattedStartTime ?><?= $session->formattedEndTime ? ' - ' . $session->formattedEndTime : '' ?>
                                        </div>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] <?= $colorClass ?>">
                                            <?= htmlspecialchars($session->eventTypeSlug) ?>
                                        </span>
                                        <?php if ($session->eventTypeSlug === 'jazz'): ?>
                                            <div class="text-[10px] text-gray-400 mt-1">
                                                <?= $session->seatsAvailable ?> seats left
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">All Events</h2>
                    <p class="text-sm text-gray-500"><?= count($events) ?> event(s) found</p>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Event
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Venue
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Sessions
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tickets
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <p>No events found</p>
                            <a href="/cms/events/create" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Create your first event
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <?php
                        /** @var \App\ViewModels\Cms\CmsEventListItemViewModel $event */
                        $colorClass = $typeColors[$event->eventTypeSlug] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($event->title) ?>
                                </div>
                                <?php if (!empty($event->shortDescription)): ?>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">
                                        <?= htmlspecialchars($event->shortDescription) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                    <?= htmlspecialchars($event->eventTypeName) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($event->venueName ?? 'Not set') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $event->sessionCount ?> session(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($event->totalCapacity > 0): ?>
                                    <?= $event->totalSoldTickets ?> / <?= $event->totalCapacity ?> sold
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/cms/events/<?= $event->eventId ?>/edit"
                                   class="text-blue-600 hover:text-blue-900 inline-flex items-center mr-3">
                                    <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                    Edit
                                </a>
                                <form method="POST" action="/cms/events/<?= $event->eventId ?>/delete"
                                      class="inline"
                                      data-confirm="Are you sure you want to delete this event? This will also deactivate all its sessions.">
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900 inline-flex items-center">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                        Delete
                                    </button>
                                </form>
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

<?php
/**
 * CMS Event edit page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsEventEditViewModel $viewModel
 * @var \App\Models\PriceTier[] $priceTiers
 * @var string|null $successMessage
 * @var string|null $errorMessage
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8">
        <!-- Header -->
        <header class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="/cms/events" class="hover:text-blue-600">Events</a>
                <span>/</span>
                <span class="text-gray-900"><?= htmlspecialchars($viewModel->title) ?></span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Edit Event</h1>
        </header>

        <!-- Messages -->
        <?php if ($successMessage): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Event Details Card -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Event Details</h2>
            </div>
            <form action="/cms/events/<?= $viewModel->eventId ?>/edit" method="POST" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="Title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="Title" id="Title"
                               value="<?= htmlspecialchars($viewModel->title) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                        <p class="px-3 py-2 bg-gray-100 rounded-lg text-gray-600">
                            <?= htmlspecialchars($viewModel->eventTypeName) ?>
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label for="ShortDescription" class="block text-sm font-medium text-gray-700 mb-1">Short
                            Description</label>
                        <textarea name="ShortDescription" id="ShortDescription" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($viewModel->shortDescription) ?></textarea>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Sessions Section -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Sessions</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        ✅ Sessions automatically appear on the public page (up to 4 days shown).
                    </p>
                </div>
                <button type="button" data-toggle="addSessionForm"
                        class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Add Session
                </button>
            </div>

            <!-- Add Session Form (hidden by default) -->
            <div id="addSessionForm" class="hidden p-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4">New Session</h3>
                <form action="/cms/events/<?= $viewModel->eventId ?>/sessions" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date/Time</label>
                            <input type="datetime-local" name="StartDateTime" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date/Time</label>
                            <input type="datetime-local" name="EndDateTime" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <?php if ($viewModel->eventTypeSlug === 'jazz'): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span class="text-purple-600">🎷</span> Seats Available
                                </label>
                                <input type="number" name="CapacityTotal" value="100" min="1"
                                       class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-purple-50"
                                       placeholder="Total seats">
                                <p class="text-xs text-purple-600 mt-1">Required for jazz events</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span class="text-purple-600">🎷</span> Hall/Stage Name
                                </label>
                                <input type="text" name="HallName" placeholder="e.g., Main Hall, Outdoor Stage"
                                       class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-purple-50">
                                <p class="text-xs text-purple-600 mt-1">Displayed as: Venue • Hall • Seats</p>
                            </div>
                        <?php else: ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                                <input type="number" name="CapacityTotal" value="100" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        <?php endif; ?>
                        <?php if ($viewModel->eventTypeSlug === 'history'): ?>
                            <div class="md:col-span-2 lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span class="text-amber-600">🏛️</span> Ticket Type Label
                                </label>
                                <input type="text" name="HistoryTicketLabel"
                                       placeholder="e.g., Group ticket - best value for 4 people"
                                       class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-amber-50">
                                <p class="text-xs text-amber-600 mt-1">Shown with a price tag icon on history tour
                                    cards</p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA Label (optional)</label>
                            <input type="text" name="CtaLabel" placeholder="e.g., Discover"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA URL (optional)</label>
                            <input type="text" name="CtaUrl" placeholder="e.g., /event/123 or #"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
                            <select name="LanguageCode"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Not specified</option>
                                <option value="NL">Dutch (NL)</option>
                                <option value="ENG">English (ENG)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Create Session
                        </button>
                        <button type="button" data-toggle="addSessionForm"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sessions List -->
            <div class="divide-y divide-gray-200">
                <?php if (empty($viewModel->sessions)): ?>
                    <div class="p-6 text-center text-gray-500">
                        No sessions yet. Click "Add Session" to create one.
                    </div>
                <?php else: ?>
                    <?php foreach ($viewModel->sessions as $session): ?>
                        <?php
                        /** @var \App\ViewModels\Cms\CmsEventSessionViewModel $session */
                        $sessionLabels = $viewModel->sessionLabels[$session->eventSessionId] ?? [];
                        $sessionPrices = $viewModel->sessionPrices[$session->eventSessionId] ?? [];
                        ?>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-medium text-gray-900">
                                        <?= $session->formattedDateLong ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?= $session->formattedStartTime ?> -
                                        <?= $session->formattedEndTime ?: 'TBD' ?>
                                    </p>
                                </div>
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/delete" method="POST"
                                      onsubmit="return confirm('Delete this session?')">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            <!-- Session Details Form -->
                            <form action="/cms/sessions/<?= $session->eventSessionId ?>" method="POST"
                                  class="mb-4">
                                <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Start</label>
                                        <input type="datetime-local" name="StartDateTime"
                                               value="<?= $session->formattedDateTimeLocal ?>"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">End</label>
                                        <input type="datetime-local" name="EndDateTime"
                                               value="<?= $session->formattedEndDateTimeLocal ?>"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <?php if ($viewModel->eventTypeSlug === 'jazz'): ?>
                                        <div>
                                            <label class="block text-xs font-medium text-purple-600 mb-1">🎷 Seats
                                                Available</label>
                                            <input type="number" name="CapacityTotal" min="0"
                                                   value="<?= $session->capacityTotal ?>"
                                                   class="w-full px-2 py-1 text-sm border border-purple-300 rounded focus:ring-1 focus:ring-purple-500 bg-purple-50">
                                            <p class="text-[10px] text-gray-500 mt-0.5">
                                                Sold: <?= $session->soldTicketsTotal ?>
                                            </p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-purple-600 mb-1">🎷
                                                Hall/Stage</label>
                                            <input type="text" name="HallName"
                                                   value="<?= htmlspecialchars($session->hallName ?? '') ?>"
                                                   placeholder="Main Hall, Outdoor Stage..."
                                                   class="w-full px-2 py-1 text-sm border border-purple-300 rounded focus:ring-1 focus:ring-purple-500 bg-purple-50">
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($viewModel->eventTypeSlug === 'history'): ?>
                                        <div>
                                            <label class="block text-xs font-medium text-amber-600 mb-1">🏛️ Ticket
                                                Label</label>
                                            <input type="text" name="HistoryTicketLabel"
                                                   value=""
                                                   placeholder="Group ticket - best value..."
                                                   class="w-full px-2 py-1 text-sm border border-amber-300 rounded focus:ring-1 focus:ring-amber-500 bg-amber-50">
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CTA Label</label>
                                        <input type="text" name="CtaLabel"
                                               value=""
                                               placeholder="Discover"
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1">CTA URL</label>
                                        <input type="text" name="CtaUrl"
                                               value=""
                                               placeholder="/event/..."
                                               class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Update Session
                                    </button>
                                </div>
                            </form>

                            <!-- Labels -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Labels</h4>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <?php foreach ($sessionLabels as $label): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-pink-100 text-pink-800 rounded text-sm">
                                                <?= htmlspecialchars($label->labelText) ?>
                                                <form action="/cms/labels/<?= $label->eventSessionLabelId ?>/delete"
                                                      method="POST" class="inline">
                                                    <input type="hidden" name="EventId"
                                                           value="<?= $viewModel->eventId ?>">
                                                    <button type="submit" class="text-pink-600 hover:text-pink-800"
                                                            title="Remove label">
                                                        &times;
                                                    </button>
                                                </form>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/labels" method="POST"
                                      class="flex gap-2">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <input type="text" name="LabelText"
                                           placeholder="New label (e.g., In Dutch, Age 16+)"
                                           class="flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-pink-600 text-white rounded hover:bg-pink-700">
                                        Add Label
                                    </button>
                                </form>
                            </div>

                            <!-- Prices -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Prices</h4>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <?php foreach ($sessionPrices as $price): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">
                                            <?php
                                                $tierName = 'Unknown';
                                                foreach ($priceTiers as $tier) {
                                                    if ($tier->priceTierId === $price->priceTierId) {
                                                        $tierName = $tier->name;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <?= htmlspecialchars($tierName) ?>:
                                            <?= htmlspecialchars($price->currencyCode) ?> <?= number_format((float)$price->price, 2) ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if (empty($sessionPrices)): ?>
                                        <span class="text-sm text-gray-500">No prices set</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Add/Update Price Form -->
                                <form action="/cms/sessions/<?= $session->eventSessionId ?>/price" method="POST"
                                      class="flex flex-wrap gap-2 items-end">
                                    <input type="hidden" name="EventId" value="<?= $viewModel->eventId ?>">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Price Tier</label>
                                        <select name="PriceTierId"
                                                class="px-2 py-1 text-sm border border-gray-300 rounded">
                                            <?php foreach ($priceTiers as $tier): ?>
                                                <option value="<?= $tier->priceTierId ?>">
                                                    <?= htmlspecialchars($tier->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Price (€)</label>
                                        <input type="text" name="Price" inputmode="decimal"
                                               pattern="[0-9]+([.,][0-9]{1,2})?"
                                               value="0.00" placeholder="12.50"
                                               class="w-24 px-2 py-1 text-sm border border-gray-300 rounded">
                                    </div>
                                    <button type="submit"
                                            class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                                        Set Price
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
<script src="/assets/js/cms/event-edit.js"></script>
</body>
</html>


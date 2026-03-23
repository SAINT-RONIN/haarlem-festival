<?php
/**
 * CMS Event create page.
 *
 * @var string $currentView
 * @var array $eventTypes
 * @var \App\Models\Venue[] $venues
 * @var \App\Models\Artist[] $artists
 * @var \App\Models\Restaurant[] $restaurants
 * @var string|null $errorMessage
 * @var string $preselectedDay Pre-selected day from URL (passed from controller)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Haarlem CMS</title>
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
                <span class="text-gray-900">Create New Event</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Create New Event</h1>
        </header>

        <!-- Error Message -->
        <?php if (!empty($errorMessage)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Create Form -->
        <form action="/cms/events" method="POST" class="max-w-2xl"
              data-jazz-type-id="<?= \App\Enums\EventTypeId::Jazz->value ?>"
              data-restaurant-type-id="<?= \App\Enums\EventTypeId::Restaurant->value ?>">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Event Details</h2>
                    <p class="text-sm text-gray-500">Fill in the basic event information</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Event Type -->
                    <div>
                        <label for="EventTypeId" class="block text-sm font-medium text-gray-700 mb-1">
                            Event Type <span class="text-red-500">*</span>
                        </label>
                        <select name="EventTypeId" id="EventTypeId" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">Select event type...</option>
                            <?php foreach ($eventTypes as $type): ?>
                                <option value="<?= $type->eventTypeId ?>">
                                    <?= htmlspecialchars($type->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            This determines which page the event appears on (Jazz, Storytelling, History, etc.)
                        </p>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="Title" class="block text-sm font-medium text-gray-700 mb-1">
                            Event Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="Title" id="Title" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border"
                               placeholder="e.g., Jazz Night with John Doe">
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label for="ShortDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Short Description
                        </label>
                        <input type="text" name="ShortDescription" id="ShortDescription"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border"
                               placeholder="Brief summary for listings">
                        <p class="mt-1 text-xs text-gray-500">
                            Used in event listings and cards
                        </p>
                    </div>

                    <!-- Long Description -->
                    <div>
                        <label for="LongDescriptionHtml" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Description
                        </label>
                        <textarea name="LongDescriptionHtml" id="LongDescriptionHtml" rows="5"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border"
                                  placeholder="Detailed event description..."></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            HTML is allowed for formatting
                        </p>
                    </div>

                    <!-- Venue -->
                    <div>
                        <label for="VenueId" class="block text-sm font-medium text-gray-700 mb-1">
                            Venue
                        </label>
                        <div class="flex gap-2">
                            <select name="VenueId" id="VenueId"
                                    class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                                <option value="">No venue selected</option>
                                <?php foreach ($venues as $venue): ?>
                                    <?php /** @var \App\Models\Venue $venue */ ?>
                                    <option value="<?= $venue->venueId ?>">
                                        <?= htmlspecialchars($venue->name) ?>
                                        <?php if (!empty($venue->addressLine)): ?>
                                            - <?= htmlspecialchars($venue->addressLine) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" data-toggle="newVenueForm"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm whitespace-nowrap">
                                + New Venue
                            </button>
                        </div>

                        <!-- Inline New Venue Form -->
                        <div id="newVenueForm" class="hidden mt-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-medium text-blue-900 mb-3">Create New Venue</h4>
                            <div class="space-y-3">
                                <div>
                                    <label for="NewVenueName" class="block text-xs font-medium text-gray-700 mb-1">
                                        Venue Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="NewVenueName"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm"
                                           placeholder="e.g., Patronaat, Jopenkerk">
                                </div>
                                <div>
                                    <label for="NewVenueAddress" class="block text-xs font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <input type="text" id="NewVenueAddress"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border text-sm"
                                           placeholder="e.g., Zijlsingel 2">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" data-action="createVenue"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        Create Venue
                                    </button>
                                    <button type="button" data-toggle="newVenueForm"
                                            class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm">
                                        Cancel
                                    </button>
                                </div>
                                <p id="venueError" class="hidden text-xs text-red-600"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Artist (shown for Jazz events only) -->
                    <div id="artistField" class="hidden">
                        <label for="ArtistId" class="block text-sm font-medium text-gray-700 mb-1">
                            Artist
                        </label>
                        <select name="ArtistId" id="ArtistId"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">No artist selected</option>
                            <?php foreach ($artists as $artist): ?>
                                <?php /** @var \App\Models\Artist $artist */ ?>
                                <option value="<?= $artist->artistId ?>">
                                    <?= htmlspecialchars($artist->name) ?>
                                    <?php if ($artist->style !== ''): ?>
                                        — <?= htmlspecialchars($artist->style) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Select the artist performing at this Jazz event.
                            <a href="/cms/artists/create" class="text-blue-600 hover:underline" target="_blank">Create a new artist</a>
                        </p>
                    </div>

                    <!-- Restaurant (shown for Restaurant events only) -->
                    <div id="restaurantField" class="hidden">
                        <label for="RestaurantId" class="block text-sm font-medium text-gray-700 mb-1">
                            Restaurant
                        </label>
                        <select name="RestaurantId" id="RestaurantId"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">No restaurant selected</option>
                            <?php foreach ($restaurants as $restaurant): ?>
                                <?php /** @var \App\Models\Restaurant $restaurant */ ?>
                                <option value="<?= $restaurant->restaurantId ?>">
                                    <?= htmlspecialchars($restaurant->name) ?>
                                    <?php if ($restaurant->city !== ''): ?>
                                        — <?= htmlspecialchars($restaurant->city) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Select the restaurant for this Restaurant event.
                            <a href="/cms/restaurants/create" class="text-blue-600 hover:underline" target="_blank">Create a new restaurant</a>
                        </p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end gap-3">
                    <a href="/cms/events"
                       class="px-4 py-2 text-gray-700 hover:text-gray-900 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors inline-flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Create Event
                    </button>
                </div>
            </div>
        </form>

        <!-- Help Box -->
        <div class="mt-6 max-w-2xl bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">What happens next?</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        After creating the event, you'll be taken to the edit page where you can:
                    </p>
                    <ul class="text-sm text-blue-700 mt-2 list-disc list-inside space-y-1">
                        <li>Add sessions with specific dates and times</li>
                        <li>Set pricing for each session</li>
                        <li>Add labels/badges (e.g., "In Dutch", "Age 16+")</li>
                        <li>Configure the CTA button text and link</li>
                        <li>For Jazz: Set available seats per session</li>
                        <li>For History: Set ticket type labels</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
<script src="/assets/js/cms/event-create.js"></script>
<script>
(function () {
    var form = document.querySelector('form[data-jazz-type-id]');
    var typeSelect = document.getElementById('EventTypeId');
    var artistField = document.getElementById('artistField');
    var restaurantField = document.getElementById('restaurantField');
    var JAZZ_TYPE = parseInt(form.dataset.jazzTypeId, 10);
    var RESTAURANT_TYPE = parseInt(form.dataset.restaurantTypeId, 10);

    function updateVisibility() {
        var val = parseInt(typeSelect.value, 10);
        artistField.classList.toggle('hidden', val !== JAZZ_TYPE);
        restaurantField.classList.toggle('hidden', val !== RESTAURANT_TYPE);
    }

    typeSelect.addEventListener('change', updateVisibility);
    updateVisibility();
}());
</script>
</body>
</html>


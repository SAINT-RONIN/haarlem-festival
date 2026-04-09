<?php
/**
 * @var \App\ViewModels\Cms\CmsEventEditViewModel $viewModel
 * @var \App\Models\PriceTier[] $priceTiers
 * @var \App\Models\Artist[] $artists
 */
?>
        <!-- Header -->
        <header class="mb-8">
            <nav class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="/cms/events" class="hover:text-blue-600">Events</a>
                <span>/</span>
                <span class="text-gray-900"><?= htmlspecialchars($viewModel->title) ?></span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Edit Event</h1>
        </header>

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
                        <label for="VenueId" class="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                        <div class="flex gap-2">
                            <select name="VenueId" id="VenueId"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">No venue selected</option>
                                <?php foreach ($viewModel->venues as $venue): ?>
                                    <?php /** @var \App\Models\Venue $venue */ ?>
                                    <option value="<?= $venue->venueId ?>"
                                        <?= $viewModel->venueId === $venue->venueId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($venue->name) ?>
                                        <?php if (!empty($venue->addressLine)): ?>
                                            - <?= htmlspecialchars($venue->addressLine) ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" data-toggle="newVenueForm"
                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm whitespace-nowrap">
                                + New Venue
                            </button>
                        </div>
                        <?php \App\View\ViewRenderer::render(__DIR__ . '/_venue-inline-form.php', []); ?>
                    </div>
                    <div class="md:col-span-2">
                        <label for="ShortDescription" class="block text-sm font-medium text-gray-700 mb-1">Short
                            Description</label>
                        <textarea name="ShortDescription" id="ShortDescription" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($viewModel->shortDescription) ?></textarea>
                    </div>

                    <?php if ($viewModel->eventTypeSlug === 'jazz'): ?>
                    <div class="md:col-span-2">
                        <label for="ArtistId" class="block text-sm font-medium text-gray-700 mb-1">Artist</label>
                        <select name="ArtistId" id="ArtistId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No artist selected</option>
                            <?php foreach ($artists as $artist): ?>
                                <?php /** @var \App\Models\Artist $artist */ ?>
                                <option value="<?= $artist->artistId ?>"
                                    <?= $viewModel->artistId === $artist->artistId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($artist->name) ?>
                                    <?php if ($artist->style !== ''): ?>
                                        — <?= htmlspecialchars($artist->style) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <a href="/cms/artists/create" class="text-blue-600 hover:underline" target="_blank">Create a new artist</a>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($viewModel->eventTypeSlug === 'restaurant'): ?>
                    <div>
                        <label for="RestaurantStars" class="block text-sm font-medium text-gray-700 mb-1">
                            <span class="text-amber-500">★</span> Restaurant Stars (1-5)
                        </label>
                        <input type="number" name="RestaurantStars" id="RestaurantStars"
                               min="1" max="5" value="<?= htmlspecialchars($viewModel->restaurantStars ?? '') ?>"
                               class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-amber-50"
                               placeholder="1-5">
                        <p class="text-xs text-amber-600 mt-1">Star rating shown on restaurant cards</p>
                    </div>
                    <div>
                        <label for="RestaurantCuisine" class="block text-sm font-medium text-gray-700 mb-1">
                            Cuisine
                        </label>
                        <input type="text" name="RestaurantCuisine" id="RestaurantCuisine"
                               value="<?= htmlspecialchars($viewModel->restaurantCuisine ?? '') ?>"
                               class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-amber-50"
                               placeholder="e.g., Dutch, fish and seafood, European">
                        <p class="text-xs text-amber-600 mt-1">Comma-separated cuisine types</p>
                    </div>
                    <div class="md:col-span-2">
                        <label for="RestaurantShortDescription" class="block text-sm font-medium text-gray-700 mb-1">
                            Short Description
                        </label>
                        <input type="text" name="RestaurantShortDescription" id="RestaurantShortDescription"
                               value="<?= htmlspecialchars($viewModel->restaurantShortDescription ?? '') ?>"
                               class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-amber-50"
                               placeholder="e.g., 3-star restaurant experience during Haarlem Festival">
                        <p class="text-xs text-amber-600 mt-1">Brief description shown on restaurant cards</p>
                    </div>
                    <?php endif; ?>

                    <?php if ($viewModel->eventTypeSlug === 'restaurant'): ?>
                    <!-- Featured Image (Restaurant only) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image</label>
                        <div class="flex items-start gap-4">
                            <div id="featuredImagePreview" class="w-32 h-24 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <?php if ($viewModel->featuredImagePath !== null): ?>
                                    <img id="featuredImagePreviewImg" src="<?= htmlspecialchars($viewModel->featuredImagePath) ?>" alt="Featured image" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs text-center px-2">No image</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col gap-2">
                                <input type="hidden" name="FeaturedImageAssetId" id="FeaturedImageAssetId"
                                       value="<?= $viewModel->featuredImageAssetId ?? '' ?>">
                                <button type="button" data-action="openEventImagePicker"
                                        class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">
                                    Choose from Library
                                </button>
                                <?php if ($viewModel->featuredImageAssetId !== null): ?>
                                    <button type="button" data-action="clearEventImage"
                                            class="px-3 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 text-sm">
                                        Remove
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <div class="mt-6 flex items-center gap-6">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="IsActive" value="1" <?= $viewModel->isActive ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        Active (visible on site)
                    </label>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Save Changes
                    </button>
                    <?php if ($viewModel->cmsDetailEditUrl !== null): ?>
                        <a href="<?= htmlspecialchars($viewModel->cmsDetailEditUrl) ?>"
                           class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                            Edit detail page content
                        </a>
                    <?php endif; ?>
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
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <span class="text-purple-600">🎷</span> Ticket Limit / Person
                                </label>
                                <input type="number" name="CapacitySingleTicketLimit" value="4" min="1"
                                       class="w-full px-3 py-2 border border-purple-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-purple-50">
                            </div>
                        <?php else: ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                                <input type="number" name="CapacityTotal" value="100" min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Limit / Person</label>
                                <input type="number" name="CapacitySingleTicketLimit" value="10" min="1"
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
                                <option value="ZH">Chinese (ZH)</option>
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
                        /** @var \App\Models\EventSessionLabel[] $sessionLabels */
                        $sessionLabels = $viewModel->sessionLabels[$session->eventSessionId] ?? [];
                        /** @var \App\ViewModels\Cms\CmsSessionPriceViewModel[] $sessionPrices */
                        $sessionPrices = $viewModel->sessionPrices[$session->eventSessionId] ?? [];
                        ?>
                        <?php \App\View\ViewRenderer::render(__DIR__ . '/_session-editor.php', [
                            'session'       => $session,
                            'sessionLabels' => $sessionLabels,
                            'sessionPrices' => $sessionPrices,
                            'viewModel'     => $viewModel,
                            'priceTiers'    => $priceTiers,
                        ]); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

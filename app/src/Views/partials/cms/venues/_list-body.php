<?php
/**
 * @var \App\Models\Venue[] $venues
 * @var ?string $successMessage
 * @var ?string $errorMessage
 */
?>
        <?php \App\View\ViewRenderer::render(__DIR__ . '/../_list-page-header.php', [
            'title'    => 'Venues',
            'subtitle' => 'Manage festival event locations',
        ]); ?>

        <!-- Add Venue Form -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Add New Venue</h2>
            </div>
            <div class="p-6">
                <div id="addVenueForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="NewVenueName" class="block text-sm font-medium text-gray-700 mb-1">
                            Venue Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="NewVenueName"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Patronaat, Jopenkerk">
                    </div>
                    <div>
                        <label for="NewVenueAddress" class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <input type="text" id="NewVenueAddress"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Zijlsingel 2">
                    </div>
                    <div>
                        <button type="button" id="addVenueBtn"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Add Venue
                        </button>
                    </div>
                </div>
                <p id="venueError" class="hidden text-sm text-red-600 mt-2"></p>
                <p id="venueSuccess" class="hidden text-sm text-green-600 mt-2"></p>
            </div>
        </div>

        <!-- Venues List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Venues (<?= count($venues) ?>)</h2>
            </div>
            <div class="divide-y divide-gray-200" id="venuesList">
                <?php if (empty($venues)): ?>
                    <div class="p-6 text-center text-gray-500">No venues yet.</div>
                <?php else: ?>
                    <?php foreach ($venues as $venue): ?>
                        <?php /** @var \App\Models\Venue $venue */ ?>
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <h3 class="font-medium text-gray-900"><?= htmlspecialchars($venue->name) ?></h3>
                                <?php if (!empty($venue->addressLine)): ?>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($venue->addressLine) ?>
                                        <?php if (!empty($venue->city)): ?>
                                            , <?= htmlspecialchars($venue->city) ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <form action="/cms/venues/<?= $venue->venueId ?>/delete" method="POST"
                                  data-confirm="Delete venue? Events using this venue will keep their current venue assignment.">
                                <button type="submit"
                                        class="px-3 py-1.5 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

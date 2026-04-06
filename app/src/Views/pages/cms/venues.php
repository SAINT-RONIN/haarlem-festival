<?php
/**
 * CMS Venues management page.
 *
 * @var string $currentView
 * @var \App\Models\Venue[] $venues
 * @var ?string $successMessage
 * @var ?string $errorMessage
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venues - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Venues</h1>
                <p class="text-sm text-gray-500 mt-1">Manage festival event locations</p>
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

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
                                  onsubmit="return confirm('Delete venue &quot;<?= htmlspecialchars($venue->name, ENT_QUOTES) ?>&quot;? Events using this venue will keep their current venue assignment.')">
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
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
<script>
(function () {
    var addBtn = document.getElementById('addVenueBtn');
    var nameInput = document.getElementById('NewVenueName');
    var addressInput = document.getElementById('NewVenueAddress');
    var errorEl = document.getElementById('venueError');
    var successEl = document.getElementById('venueSuccess');

    addBtn.addEventListener('click', async function () {
        var name = nameInput.value.trim();
        var address = addressInput.value.trim();

        errorEl.classList.add('hidden');
        successEl.classList.add('hidden');

        if (!name) {
            errorEl.textContent = 'Venue name is required';
            errorEl.classList.remove('hidden');
            return;
        }

        try {
            var response = await fetch('/cms/venues', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'VenueName=' + encodeURIComponent(name) + '&AddressLine=' + encodeURIComponent(address)
            });
            var data = await response.json();

            if (data.success) {
                nameInput.value = '';
                addressInput.value = '';
                window.location.reload();
            } else {
                errorEl.textContent = data.errors ? data.errors.join(', ') : 'Failed to create venue';
                errorEl.classList.remove('hidden');
            }
        } catch (err) {
            errorEl.textContent = 'An error occurred. Please try again.';
            errorEl.classList.remove('hidden');
        }
    });

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}());
</script>
</body>
</html>

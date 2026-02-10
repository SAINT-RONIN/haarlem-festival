<?php
/**
 * CMS Schedule Days Management Page.
 *
 * @var string $currentView
 * @var array $eventTypes
 * @var array $dayConfigs
 * @var string|null $successMessage
 * @var string|null $errorMessage
 */

$dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// Group configs by event type
$globalConfigs = [];
$typeConfigs = [];
foreach ($dayConfigs as $config) {
    if ($config['EventTypeId'] === null) {
        $globalConfigs[$config['DayOfWeek']] = $config;
    } else {
        $typeConfigs[$config['EventTypeId']][$config['DayOfWeek']] = $config;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Day Visibility - CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Schedule Day Visibility</h1>
            <p class="text-gray-600 mt-1">Control which days are visible in the schedule for each event type.</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Global Settings -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">🌐 Global Settings</h2>
                <p class="text-sm text-gray-500">These settings apply to all event types unless overridden below.</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-7 gap-3">
                    <?php for ($day = 0; $day <= 6; $day++): ?>
                        <?php $isVisible = (bool)($globalConfigs[$day]['IsVisible'] ?? 1); ?>
                        <form method="POST" action="/cms/schedule-days/toggle" class="text-center">
                            <input type="hidden" name="EventTypeId" value="">
                            <input type="hidden" name="DayOfWeek" value="<?= $day ?>">
                            <input type="hidden" name="IsVisible" value="<?= $isVisible ? 0 : 1 ?>">
                            <button type="submit"
                                    class="w-full p-3 rounded-lg border-2 transition-colors <?= $isVisible ? 'bg-green-100 border-green-500 text-green-800' : 'bg-gray-100 border-gray-300 text-gray-500' ?>">
                                <span class="block text-sm font-medium"><?= $dayNames[$day] ?></span>
                                <span class="block text-xs mt-1"><?= $isVisible ? '✓ Visible' : '✗ Hidden' ?></span>
                            </button>
                        </form>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- Per Event Type Settings -->
        <?php foreach ($eventTypes as $eventType): ?>
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <?= htmlspecialchars($eventType['Name']) ?>
                    </h2>
                    <p class="text-sm text-gray-500">Override global settings for this event type only.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-7 gap-3">
                        <?php for ($day = 0; $day <= 6; $day++): ?>
                            <?php
                            $typeConfig = $typeConfigs[$eventType['EventTypeId']][$day] ?? null;
                            $globalVisible = (bool)($globalConfigs[$day]['IsVisible'] ?? 1);
                            $isVisible = $typeConfig !== null ? (bool)$typeConfig['IsVisible'] : $globalVisible;
                            $isOverridden = $typeConfig !== null;
                            ?>
                            <form method="POST" action="/cms/schedule-days/toggle" class="text-center">
                                <input type="hidden" name="EventTypeId" value="<?= $eventType['EventTypeId'] ?>">
                                <input type="hidden" name="DayOfWeek" value="<?= $day ?>">
                                <input type="hidden" name="IsVisible" value="<?= $isVisible ? 0 : 1 ?>">
                                <button type="submit"
                                        class="w-full p-3 rounded-lg border-2 transition-colors <?= $isVisible ? 'bg-green-100 border-green-500 text-green-800' : 'bg-gray-100 border-gray-300 text-gray-500' ?> <?= $isOverridden ? 'ring-2 ring-blue-400' : '' ?>">
                                    <span class="block text-sm font-medium"><?= $dayNames[$day] ?></span>
                                    <span class="block text-xs mt-1">
                                        <?= $isVisible ? '✓ Visible' : '✗ Hidden' ?>
                                        <?= $isOverridden ? ' (override)' : '' ?>
                                    </span>
                                </button>
                            </form>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Help Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 mt-0.5"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-900">How it works</h3>
                    <ul class="text-sm text-blue-700 mt-1 list-disc list-inside space-y-1">
                        <li><strong>Global settings</strong> apply to all event types by default.</li>
                        <li><strong>Per-event-type settings</strong> override global settings for that specific type.
                        </li>
                        <li>Hidden days will not appear in the public schedule, even if they have events.</li>
                        <li>Blue ring indicates an override is active for that event type.</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    lucide.createIcons();
</script>
</body>
</html>


<?php
/**
 * Shared list-page header for CMS management screens.
 *
 * Used by: events, users, orders, artists, venues list body partials.
 *
 * @var string      $title         Page heading (e.g. "Events Management").
 * @var string      $subtitle      Secondary description line.
 * @var string|null $createUrl     URL for the "Create X" button; null = no button.
 * @var string|null $createLabel   Button label text (e.g. "Create Event").
 * @var string      $createIcon    Lucide icon name for the button (default: "plus").
 */

$createUrl   ??= null;
$createLabel ??= null;
$createIcon  ??= 'plus';
?>
<header class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($title) ?></h1>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($subtitle) ?></p>
        </div>
        <?php if ($createUrl !== null && $createLabel !== null): ?>
            <a href="<?= htmlspecialchars($createUrl) ?>"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="<?= htmlspecialchars($createIcon) ?>" class="w-4 h-4" aria-hidden="true"></i>
                <?= htmlspecialchars($createLabel) ?>
            </a>
        <?php endif; ?>
    </div>
</header>

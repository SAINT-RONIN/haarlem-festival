<?php
/**
 * CMS Edit Item Text - Text input field with character counter.
 *
 * @var array $item Item data with itemId, displayName, type, value, maxChars
 */

$itemId = $item['itemId'];
$inputId = 'item-' . $itemId;
$isTextarea = strlen($item['value']) > 100 || $item['type'] === 'TEXT';
?>

<div class="space-y-2">
    <div class="flex items-center justify-between">
        <label for="<?= $inputId ?>" class="text-sm font-medium text-gray-700">
            <?= htmlspecialchars($item['displayName']) ?>
        </label>
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 text-xs bg-blue-50 text-blue-600 rounded">
                <?= htmlspecialchars($item['typeLabel']) ?>
            </span>
            <span id="<?= $inputId ?>-counter" class="char-counter text-xs text-gray-500">
                0 / <?= $item['maxChars'] ?>
            </span>
        </div>
    </div>

    <?php if ($isTextarea): ?>
        <textarea 
            id="<?= $inputId ?>"
            name="items[<?= $itemId ?>]"
            data-char-limit="<?= $item['maxChars'] ?>"
            data-item-type="<?= htmlspecialchars($item['type']) ?>"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-colors"
            placeholder="Enter <?= htmlspecialchars(strtolower($item['displayName'])) ?>..."
        ><?= htmlspecialchars($item['value']) ?></textarea>
    <?php else: ?>
        <input 
            type="text"
            id="<?= $inputId ?>"
            name="items[<?= $itemId ?>]"
            value="<?= htmlspecialchars($item['value']) ?>"
            data-char-limit="<?= $item['maxChars'] ?>"
            data-item-type="<?= htmlspecialchars($item['type']) ?>"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            placeholder="Enter <?= htmlspecialchars(strtolower($item['displayName'])) ?>..."
        >
    <?php endif; ?>

    <p class="text-xs text-gray-400">
        Maximum <?= $item['maxChars'] ?> characters
    </p>
</div>


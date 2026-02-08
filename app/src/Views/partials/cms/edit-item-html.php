<?php
/**
 * CMS Edit Item HTML - TinyMCE rich text editor.
 *
 * @var array $item Item data with itemId, displayName, type, value, maxChars
 */

$itemId = $item['itemId'];
$inputId = 'item-' . $itemId;
?>

<div class="space-y-2">
    <div class="flex items-center justify-between">
        <label for="<?= $inputId ?>" class="text-sm font-medium text-gray-700">
            <?= htmlspecialchars($item['displayName']) ?>
        </label>
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 text-xs bg-purple-50 text-purple-600 rounded">
                <?= htmlspecialchars($item['typeLabel']) ?>
            </span>
            <span id="<?= $inputId ?>-counter" class="char-counter text-xs text-gray-500">
                0 / <?= $item['maxChars'] ?>
            </span>
        </div>
    </div>

    <textarea 
        id="<?= $inputId ?>"
        name="items[<?= $itemId ?>]"
        data-tinymce
        data-char-limit="<?= $item['maxChars'] ?>"
        data-item-type="<?= htmlspecialchars($item['type']) ?>"
        class="w-full"
    ><?= htmlspecialchars($item['value']) ?></textarea>

    <p class="text-xs text-gray-400">
        Rich text editor • Maximum <?= $item['maxChars'] ?> characters (plain text count)
    </p>
</div>


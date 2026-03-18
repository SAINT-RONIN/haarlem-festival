<?php
/**
 * CMS Edit Item Image - File upload with preview.
 *
 * @var array $item Item data with itemId, displayName, mediaAsset
 * @var array $imageLimits Image limits from viewModel
 */
$itemId = $item['itemId'];
$inputId = 'item-' . $itemId;
$mediaAsset = $item['mediaAsset'];

$rawFilePath = is_array($mediaAsset) ? (string)($mediaAsset['FilePath'] ?? '') : '';
$previewSrc = '';
if ($rawFilePath !== '') {
    $path = parse_url($rawFilePath, PHP_URL_PATH);
    $query = parse_url($rawFilePath, PHP_URL_QUERY);

    if (is_string($path) && $path !== '') {
        $segments = array_map('rawurlencode', explode('/', ltrim($path, '/')));
        $previewSrc = '/' . implode('/', $segments);
        if (is_string($query) && $query !== '') {
            $previewSrc .= '?' . $query;
        }
    }
}
?>
<div class="space-y-3">
    <div class="flex items-center justify-between">
        <label for="<?= $inputId ?>" class="text-sm font-medium text-gray-700">
            <?= htmlspecialchars($item['displayName']) ?>
        </label>
        <span class="px-2 py-0.5 text-xs bg-green-50 text-green-600 rounded">
            Image
        </span>
    </div>
    <div class="flex items-start gap-6">
        <div id="preview-<?= $itemId ?>" class="flex-shrink-0">
            <?php if ($previewSrc !== ''): ?>
                <img src="<?= htmlspecialchars($previewSrc) ?>" 
                     class="max-h-40 rounded-lg border border-gray-200" 
                     alt="<?= htmlspecialchars($mediaAsset['AltText'] ?? 'Current image') ?>">
            <?php else: ?>
                <div class="w-40 h-32 bg-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                    <i data-lucide="image" class="w-8 h-8 text-gray-400"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-1 space-y-3">
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <input type="file" id="<?= $inputId ?>" accept="image/jpeg,image/png,image/webp" onchange="uploadImage(<?= $itemId ?>, this)" class="hidden">
                    <label for="<?= $inputId ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 cursor-pointer transition-colors">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Choose Image
                    </label>
                </div>

                <!-- Media library button -->
                <button type="button" onclick="openMediaLibrary(<?= $itemId ?>)" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    Media Library
                </button>
            </div>
            <div class="text-xs text-gray-500 space-y-1">
                <p><strong>Allowed types:</strong> JPG, PNG, WebP</p>
                <p><strong>Max size:</strong> <?= htmlspecialchars($imageLimits['maxFileSizeFormatted']) ?></p>
                <p><strong>Max dimensions:</strong> <?= $imageLimits['maxWidth'] ?>x<?= $imageLimits['maxHeight'] ?>px</p>
            </div>
            <?php if ($mediaAsset && !empty($mediaAsset['OriginalFileName'])): ?>
                <p class="text-xs text-gray-400">Current: <?= htmlspecialchars($mediaAsset['OriginalFileName']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

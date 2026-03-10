<?php
/**
 * CMS Edit Section Accordion - Collapsible section with editable items.
 *
 * @var array $section Section data with items
 */
?>

<div class="accordion-section bg-white border border-gray-200 rounded-xl overflow-hidden">
    <!-- Section Header -->
    <button type="button" 
            data-accordion-toggle
            class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
        <div class="flex items-center gap-3">
            <i data-lucide="layout" class="w-5 h-5 text-blue-600"></i>
            <span class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($section['displayName']) ?></span>
            <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">
                <?= count($section['items']) ?> items
            </span>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform"></i>
    </button>

    <!-- Section Content -->
    <div class="accordion-content hidden p-6 border-t border-gray-200 space-y-6">
        <?php foreach ($section['items'] as $item): ?>
            <?php
            $inputType = $item['inputType'];
            if ($inputType === 'tinymce') {
                require __DIR__ . '/edit-item-html.php';
            } elseif ($inputType === 'file') {
                require __DIR__ . '/edit-item-image.php';
            } else {
                require __DIR__ . '/edit-item-text.php';
            }
            ?>
        <?php endforeach; ?>
    </div>
</div>


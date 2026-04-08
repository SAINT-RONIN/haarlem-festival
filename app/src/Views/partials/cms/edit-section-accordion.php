<?php
/**
 * CMS Edit Section Accordion - Collapsible section with editable items.
 *
 * @var \App\ViewModels\Cms\CmsSectionDisplayViewModel $section
 */

use App\Constants\CmsEditorStyles;

$colorMap     = CmsEditorStyles::COLOR_MAP;
$defaultColor = CmsEditorStyles::DEFAULT_COLOR;
$columnClassMap = CmsEditorStyles::COLUMN_CLASS_MAP;
?>

<div id="section-<?= htmlspecialchars($section->key) ?>" class="accordion-section bg-white border border-gray-200 rounded-xl overflow-hidden">
    <!-- Section Header -->
    <button type="button"
            data-accordion-toggle
            class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
        <div class="flex items-center gap-3">
            <i data-lucide="layout" class="w-5 h-5 text-blue-600"></i>
            <span class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($section->displayName) ?></span>
            <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">
                <?= count($section->items) ?> items
            </span>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform"></i>
    </button>

    <!-- Section Content -->
    <div class="accordion-content hidden border-t border-gray-200">
        <?php if ($section->subGroups !== null): ?>
            <!-- Grouped card layout -->
            <div class="p-4 sm:p-6 space-y-6">
                <?php foreach ($section->subGroups as $group):
                    $colors = $colorMap[$group->color] ?? $defaultColor;
                    $gridClass = $columnClassMap[$group->columns] ?? $columnClassMap[1];
                    ?>
                    <div class="rounded-lg border border-gray-200 <?= $colors['border'] ?> border-l-4 overflow-hidden">
                        <!-- Group header -->
                        <div class="px-4 py-3 <?= $colors['bg'] ?> flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i data-lucide="<?= htmlspecialchars($group->icon) ?>" class="w-4 h-4 <?= $colors['icon'] ?>"></i>
                                <h4 class="text-sm font-semibold <?= $colors['text'] ?>">
                                    <?= htmlspecialchars($group->label) ?>
                                </h4>
                            </div>
                            <span class="px-2 py-0.5 text-xs <?= $colors['bg'] ?> <?= $colors['text'] ?> rounded-full border border-current/20">
                                <?= count($group->items) ?> fields
                            </span>
                        </div>
                        <!-- Group items in grid -->
                        <div class="p-4 grid <?= $gridClass ?> gap-4">
                            <?php foreach ($group->items as $item): ?>
                                <?php
                                    $inputType = $item->inputType;
                                $isFullWidth = $inputType === 'tinymce' || $inputType === 'file';
                                ?>
                                <div class="<?= $isFullWidth ? 'col-span-full' : '' ?>">
                                    <?php
                                    if ($inputType === 'tinymce') {
                                        require __DIR__ . '/edit-item-html.php';
                                    } elseif ($inputType === 'file') {
                                        require __DIR__ . '/edit-item-image.php';
                                    } else {
                                        require __DIR__ . '/edit-item-text.php';
                                    }
                                ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Flat layout (default for non-schedule sections) -->
            <div class="p-6 space-y-6">
                <?php foreach ($section->items as $item): ?>
                    <?php
                    $inputType = $item->inputType;
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
        <?php endif; ?>

        <?php if (isset($jazzLineupManager) && $jazzLineupManager instanceof \App\ViewModels\Cms\CmsJazzLineupManagerViewModel && $section->key === $jazzLineupManager->sectionKey): ?>
            <?php require __DIR__ . '/jazz-lineup-manager.php'; ?>
        <?php endif; ?>
    </div>
</div>

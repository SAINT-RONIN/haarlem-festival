<?php
/**
 * CMS Edit Section Accordion - Collapsible section with editable items.
 *
 * @var \App\ViewModels\Cms\CmsSectionDisplayViewModel $section
 */

$colorMap = [
    'blue' => ['border' => 'border-l-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => 'text-blue-500'],
    'amber' => ['border' => 'border-l-amber-500', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'text-amber-500'],
    'emerald' => ['border' => 'border-l-emerald-500', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'text-emerald-500'],
    'purple' => ['border' => 'border-l-purple-500', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'icon' => 'text-purple-500'],
    'rose' => ['border' => 'border-l-rose-500', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'icon' => 'text-rose-500'],
];
$defaultColor = $colorMap['blue'];

$columnClassMap = [
    1 => 'grid-cols-1',
    2 => 'grid-cols-1 md:grid-cols-2',
    3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
];
?>

<div class="accordion-section bg-white border border-gray-200 rounded-xl overflow-hidden">
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
    </div>
</div>

<?php
/**
 * Shared metadata block for checkout status pages.
 *
 * @var array<string, string> $details
 */
$details ??= [];

if ($details === []) {
    return;
}
?>
<div class="mt-6 p-4 bg-[#ECE6DD] rounded-2xl text-sm sm:text-base text-gray-800 font-['Montserrat'] leading-6">
    <?php foreach ($details as $label => $value): ?>
        <p>
            <strong><?= htmlspecialchars($label) ?>:</strong>
            <?= htmlspecialchars($value) ?>
        </p>
    <?php endforeach; ?>
</div>


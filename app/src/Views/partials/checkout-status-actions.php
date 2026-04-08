<?php
/**
 * Shared action buttons for checkout status pages.
 *
 * @var string $primaryHref
 * @var string $primaryLabel
 * @var string $secondaryHref
 * @var string $secondaryLabel
 */
$primaryHref ??= '/';
$primaryLabel ??= 'Continue';
$secondaryHref ??= '/my-program';
$secondaryLabel ??= 'Back';
?>
<div class="mt-8 flex flex-wrap gap-3">
    <a href="<?= htmlspecialchars($primaryHref) ?>"
       class="px-5 py-3 bg-slate-800 text-stone-100 rounded-xl hover:bg-slate-700 transition-colors duration-200 font-['Montserrat']">
        <?= htmlspecialchars($primaryLabel) ?>
    </a>
    <a href="<?= htmlspecialchars($secondaryHref) ?>"
       class="px-5 py-3 bg-white text-slate-800 rounded-xl outline outline-2 outline-offset-[-2px] outline-slate-800 hover:bg-slate-50 transition-colors duration-200 font-['Montserrat']">
        <?= htmlspecialchars($secondaryLabel) ?>
    </a>
</div>


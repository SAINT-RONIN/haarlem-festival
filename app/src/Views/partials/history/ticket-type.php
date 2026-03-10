<?php
/**
 * Partial for rendering a single pricing ticket card (Single / Group) on the History page.
 *
 * Expects a \App\ViewModels\History\PricingCard instance as $card.
 *
 * @var \App\ViewModels\History\PricingCard $card
 */

use App\ViewModels\History\PricingCard;

/** @var PricingCard $card */
?>
<div class="self-stretch p-20 bg-white rounded-[10px] shadow-[0px_0px_30px_0px_rgba(0,0,0,0.10)] inline-flex flex-col justify-center items-center gap-6 overflow-hidden">
    <div class="inline-flex justify-start items-center gap-6">
        <?php if (!empty($card->icon)): ?>
            <div class="w-20 h-20 relative overflow-hidden flex-shrink-0">
                <img
                    src="<?= htmlspecialchars($card->icon) ?>"
                    alt="<?= htmlspecialchars($card->title) ?> icon"
                    class="w-full h-full object-contain" />
            </div>
        <?php endif; ?>
        <div class="inline-flex flex-col justify-start items-start gap-2.5">
            <div class="justify-start text-slate-800 text-4xl font-medium font-['Montserrat']">
                <?= htmlspecialchars($card->title) ?>
            </div>
            <div class="justify-start text-slate-800 text-4xl font-medium font-['Montserrat']">
                <?= htmlspecialchars($card->price) ?>
            </div>
        </div>
    </div>
    <ul class="list-disc pl-5 text-slate-800 text-lg font-normal font-['Montserrat'] leading-8">
        <?php foreach ($card->descriptionItems as $line): ?>
            <li><?= htmlspecialchars($line) ?></li>
        <?php endforeach; ?>
    </ul>
</div>

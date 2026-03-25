<?php
/**
 * Single pricing card partial.
 *
 * @var \App\ViewModels\PricingCardData $card
 */

$bgClass = $card->isHighlighted ? 'bg-sand' : 'bg-white';
$borderClass = $card->isHighlighted ? 'border-black' : 'border-royal-blue';
?>

<div class="flex-1 pb-6 <?= $bgClass ?> rounded-2xl shadow-md flex flex-col justify-start items-start overflow-hidden">
    <!-- Header -->
    <div class="self-stretch p-6 bg-royal-blue flex justify-start items-start gap-2.5">
        <h3 class="flex-1 text-white text-2xl sm:text-3xl font-medium font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($card->title) ?>
        </h3>
    </div>

    <!-- Content -->
    <div class="self-stretch p-6 flex flex-col justify-start items-start gap-4 sm:gap-6">
        <?php if (!empty($card->items)): ?>
            <!-- Items List -->
            <div class="self-stretch flex flex-col justify-start items-start gap-3.5">
                <?php foreach ($card->items as $item): ?>
                    <div class="self-stretch p-2.5 bg-gray-50 rounded-md shadow-sm flex flex-col justify-start items-start">
                        <div class="self-stretch flex flex-col justify-start items-start gap-3.5">
                            <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-5">
                                <?= htmlspecialchars($item->name) ?>
                            </p>
                            <?php if ($item->capacity !== ''): ?>
                                <p class="self-stretch text-royal-blue text-lg sm:text-xl font-normal font-['Montserrat'] leading-tight">
                                    <?= htmlspecialchars($item->capacity) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php if ($item->price !== ''): ?>
                            <div class="self-stretch flex justify-end items-end gap-2 mt-2">
                                <span class="text-royal-blue text-lg sm:text-xl font-medium font-['Montserrat'] leading-tight">
                                    <?= htmlspecialchars($item->price) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Price Display -->
            <div class="self-stretch flex flex-col justify-start items-start gap-2.5">
                <p class="self-stretch text-<?= $card->isHighlighted ? 'black' : 'royal-blue' ?> text-3xl sm:text-4xl font-medium font-['Montserrat'] leading-10 tracking-tight">
                    <?= htmlspecialchars($card->price) ?>
                </p>
                <p class="self-stretch text-<?= $card->isHighlighted ? 'black' : 'royal-blue' ?> text-lg sm:text-xl font-normal font-['Montserrat']">
                    <?= htmlspecialchars($card->priceDescription) ?>
                </p>
            </div>

            <!-- Includes -->
            <?php if (!empty($card->includes)): ?>
                <div class="self-stretch pt-4 border-t-2 <?= $borderClass ?> flex flex-col justify-start items-start gap-3">
                    <p class="self-stretch text-<?= $card->isHighlighted ? 'black' : 'royal-blue' ?> text-lg sm:text-xl font-medium font-['Montserrat'] leading-tight">
                        INCLUDES
                    </p>
                    <?php foreach ($card->includes as $include): ?>
                        <p class="self-stretch text-<?= $card->isHighlighted ? 'black' : 'royal-blue' ?> text-base sm:text-lg font-normal font-['Montserrat'] leading-5">
                            • <?= htmlspecialchars($include) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Additional Info -->
            <?php if ($card->additionalInfo): ?>
                <p class="self-stretch text-<?= $card->isHighlighted ? 'black' : 'royal-blue' ?> text-base sm:text-lg font-normal font-['Montserrat']">
                    <?= htmlspecialchars($card->additionalInfo) ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>


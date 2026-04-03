<?php
/**
 * Restaurant Instructions section partial.
 * Shows "How reservations work" with numbered step cards.
 *
 * Expects a \App\ViewModels\Restaurant\RestaurantPageViewModel as $viewModel
 * and uses its instructionsSection property.
 *
 * @var \App\ViewModels\Restaurant\InstructionsSectionData $instructionsSection
 */

use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\InstructionCardData;

/** @var InstructionsSectionData $instructionsSection */
$title = $instructionsSection->title;
$cards = $instructionsSection->cards;
?>

<section id="how-it-works" class="self-stretch px-4 sm:px-8 md:px-12 lg:px-16 xl:px-24 py-8 sm:py-12 md:py-16 lg:py-20 xl:py-12 flex flex-col justify-start items-start gap-8 sm:gap-10 md:gap-12">
    <!-- Section Title -->
    <h2 class="text-gray-900 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
        <?= htmlspecialchars($title) ?>
    </h2>

    <!-- Instruction Cards -->
    <div class="self-stretch flex flex-col lg:flex-row justify-center items-stretch gap-6 sm:gap-8 md:gap-10 lg:gap-16 xl:gap-24">

        <?php foreach ($cards as $card): ?>
            <?php /** @var InstructionCardData $card */ ?>
            <?php
            $number = $card->number;
            $cardTitle = $card->title;
            $cardText = $card->text;
            $icon = $card->icon;
            ?>

            <div class="flex-1 max-w-sm mx-auto lg:mx-0 p-5 bg-white rounded-3xl flex flex-col justify-start items-start gap-3 shadow-sm">
                <div class="self-stretch inline-flex justify-start items-center gap-5">
                    <div class="w-11 h-7 bg-slate-800 rounded-full flex justify-center items-center">
                        <span class="text-white text-lg font-bold font-['Montserrat'] leading-5"><?= htmlspecialchars($number) ?></span>
                    </div>
                </div>
                <div class="self-stretch flex flex-col justify-center items-center gap-3">
                    <div class="w-16 h-14 bg-stone-100 rounded-full flex justify-center items-center">
                        <img
                            src="/assets/Icons/Restaurant/<?= htmlspecialchars($icon) ?>-icon.svg"
                            alt="<?= htmlspecialchars($icon) ?> icon"
                            class="w-6 h-6"
                            aria-hidden="true">
                    </div>
                    <div class="self-stretch flex flex-col justify-start items-start gap-1.5">
                        <h3 class="self-stretch text-center text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat']">
                            <?= htmlspecialchars($cardTitle) ?>
                        </h3>
                        <p class="self-stretch text-center text-slate-600 text-sm sm:text-base font-normal font-['Montserrat'] leading-relaxed">
                            <?= htmlspecialchars($cardText) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</section>

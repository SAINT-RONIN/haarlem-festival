<?php
/**
 * "Read more about these locations" section for the History page.
 *
 * Expects a \App\ViewModels\History\HistoryPageViewModel as $viewModel
 * and uses its venuesData property.
 *
 * @var \App\ViewModels\History\HistoryPageViewModel $viewModel
 */

use App\ViewModels\History\VenuesData;
use App\ViewModels\History\VenueCardData;

/** @var VenuesData $venuesData */
$venuesData = $viewModel->venuesData;
$heading = $venuesData->headingText;
$venues  = $venuesData->venues;
?>
<section class="w-full px-6 lg:px-24 py-12 flex flex-col justify-start items-start gap-12 overflow-hidden">
    <div class="text-slate-800 text-5xl font-bold font-['Montserrat'] leading-[62px]">
        <?= htmlspecialchars($heading) ?>
    </div>
    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-8 xl:gap-12">
        <?php foreach ($venues as $venue): ?>
            <?php /** @var VenueCardData $venue */ ?>
            <article class="w-full bg-white rounded-2xl shadow-[0px_0px_24px_-2px_rgba(0,0,0,0.25)] flex flex-col justify-start items-stretch overflow-hidden">
                <img
                    class="w-full aspect-[541/510] p-2.5 object-cover"
                    src="<?= htmlspecialchars($venue->imageUrl) ?>"
                    alt="<?= htmlspecialchars($venue->name) ?>" />
                <div class="w-full p-6 flex-1 flex flex-col justify-between items-start gap-5 overflow-hidden">
                    <div class="w-full flex flex-col justify-start items-start gap-3">
                        <div class="w-full text-slate-800 text-2xl font-semibold font-['Montserrat']">
                            <?= htmlspecialchars($venue->name) ?>
                        </div>
                        <div class="w-full text-slate-800 text-lg font-normal font-['Montserrat']">
                            <?= htmlspecialchars($venue->description) ?>
                        </div>
                    </div>

                    <a href="<?= htmlspecialchars($venue->venueUrl) ?>" class="w-full h-11 px-4 bg-slate-800 rounded-[5px] inline-flex justify-between items-center mt-auto">
                        <div class="flex-1 text-white text-xl font-normal font-['Montserrat'] leading-5">
                            <?= htmlspecialchars($venuesData->viewMoreLabel) ?>
                        </div>

                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

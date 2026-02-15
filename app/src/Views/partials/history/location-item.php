<?php
/**
 * Partial for rendering a single location card in the History locations section.
 *
 * Expects a \App\ViewModels\History\VenueCardData instance as $venue.
 *
 * @var \App\ViewModels\History\VenueCardData $venue
 */

use App\ViewModels\History\VenueCardData;

/** @var VenueCardData $venue */
?>
<article class="flex-1 min-w-[280px] max-w-sm bg-white rounded-2xl shadow-[0px_0px_24px_-2px_rgba(0,0,0,0.25)] inline-flex flex-col justify-start items-start overflow-hidden">
    <img
        class="w-full aspect-[541/510] p-2.5 object-cover"
        src="<?= htmlspecialchars($venue->imageUrl) ?>"
        alt="<?= htmlspecialchars($venue->name) ?>" />
    <div class="w-full p-6 flex flex-col justify-start items-start gap-5 overflow-hidden">
        <div class="w-full flex flex-col justify-start items-start">
            <div class="w-full text-slate-800 text-2xl font-semibold font-['Montserrat']">
                <?= htmlspecialchars($venue->name) ?>
            </div>
        </div>
        <div class="w-full text-slate-800 text-lg font-normal font-['Montserrat']">
            <?= htmlspecialchars($venue->description) ?>
        </div>
        <div class="w-full h-11 px-4 bg-slate-800 rounded-[5px] inline-flex justify-between items-center">
            <div class="flex-1 text-white text-xl font-normal font-['Montserrat'] leading-5">
                View more
            </div>
        </div>
    </div>
</article>

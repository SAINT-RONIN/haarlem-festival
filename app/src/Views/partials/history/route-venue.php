<?php
/**
 * Partial for rendering a single History route venue inside the route section.
 *
 * Expects a \App\ViewModels\History\RouteVenue instance as $venue
 * and the zero-based index $index for numbering.
 *
 * @var \App\ViewModels\History\RouteVenue $venue
 * @var int $index
 */

use App\ViewModels\History\RouteVenue;

/** @var RouteVenue $venue */
?>
<div class="self-stretch inline-flex justify-start items-start gap-[5px]">
    <div class="flex-1 p-3.5 bg-white rounded-[10px] outline outline-[0.50px] outline-offset-[-0.50px] outline-slate-800 inline-flex flex-col justify-start items-start gap-[5px] overflow-hidden">
        <div class="self-stretch justify-start text-slate-800 text-base font-bold font-['Montserrat'] leading-4">
            <?= ($index + 1) . '. ' . htmlspecialchars($venue->venueName) ?>
        </div>
        <div class="self-stretch justify-start text-slate-800 text-base font-light font-['Montserrat']">
            <?= htmlspecialchars($venue->venueDescription) ?>
        </div>
    </div>
    <div class="w-9 self-stretch p-3.5 <?= htmlspecialchars($venue->venueBadgeColor) ?> rounded-[10px] border-2 border-slate-800/70"></div>
</div>
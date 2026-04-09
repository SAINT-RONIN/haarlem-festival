<?php
/**
 * My-Program left column: selected events list with empty state and per-item cards.
 *
 * @var \App\ViewModels\Program\MyProgramPageViewModel $viewModel
 */

use App\View\ViewRenderer;
?>
<div class="w-full lg:flex-1 p-3 sm:p-5 bg-[#ECE6DD] rounded-3xl flex flex-col gap-5">

    <!-- Header Bar -->
    <div class="px-4 pt-4 pb-4 bg-white/60 rounded-2xl">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-pink-700 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <h2 class="text-slate-800 text-xl sm:text-2xl font-bold font-['Montserrat'] leading-8">
                <?= htmlspecialchars($viewModel->selectedEventsHeading) ?>
            </h2>
        </div>
    </div>

    <!-- Info Box -->
    <div class="p-4 bg-white/60 rounded-2xl inline-flex justify-center items-center gap-2.5">
        <svg class="w-8 h-8 text-amber-600 flex-shrink-0" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <p class="flex-1 text-slate-800 text-xs sm:text-sm font-normal font-['Montserrat']">
            <?= htmlspecialchars($viewModel->payWhatYouLikeMessage) ?>
        </p>
    </div>

    <!-- Clear Button -->
    <div class="flex justify-end px-2 sm:px-6">
        <div class="flex-1 flex justify-end items-center gap-2.5">
            <span class="text-slate-800 text-sm font-light font-['Montserrat'] leading-4 uppercase">
                <?= htmlspecialchars($viewModel->clearButtonText) ?>
            </span>
            <button type="button"
                    id="js-clear-program"
                    class="w-10 h-10 p-2.5 bg-rose-50 rounded-[10px] outline outline-2 outline-offset-[-2px] outline-red-200
                           flex items-center justify-center hover:bg-rose-100 transition-colors overflow-hidden"
                    aria-label="Clear program">
                <svg class="w-6 h-6 text-red-400" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Column Headers (desktop only) -->
    <div class="hidden xl:grid px-6 grid-cols-[6rem_1fr_5rem_6rem_6rem_5rem_2.25rem] gap-4 items-center">
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3"></span>
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3">Event(s)</span>
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3 text-center">Price</span>
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3 text-center">Quantity</span>
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3 text-center">Donation(s)</span>
        <span class="text-slate-800 text-xs font-light font-['Montserrat'] uppercase leading-3 text-center">Sum</span>
        <span></span>
    </div>

    <!-- Divider (desktop only) -->
    <div class="hidden xl:block mx-4 h-0 outline outline-[1.5px] outline-offset-[-0.75px] outline-neutral-700"></div>

    <!-- Item Rows -->
    <?php if (empty($viewModel->items)): ?>
        <div class="px-6 py-12 text-center">
            <p class="text-slate-500 text-base font-['Montserrat']">No events in your program yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($viewModel->items as $item): ?>
            <?php ViewRenderer::render(__DIR__ . '/_my-program-event-card-desktop.php', ['item' => $item]); ?>
            <?php ViewRenderer::render(__DIR__ . '/_my-program-event-card-mobile.php', ['item' => $item]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Continue Exploring Button -->
    <a href="/"
       class="self-stretch p-3 sm:p-3.5 bg-slate-800 rounded-2xl inline-flex justify-center items-center
              hover:bg-slate-700 transition-colors duration-200">
        <svg class="w-5 h-5 text-stone-100" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span class="text-stone-100 text-base sm:text-xl font-normal font-['Montserrat'] leading-5">
            <?= htmlspecialchars($viewModel->continueExploringText) ?>
        </span>
    </a>
</div>

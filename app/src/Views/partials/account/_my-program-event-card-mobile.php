<?php
/**
 * My-Program single event card — mobile/tablet layout (<xl breakpoint).
 *
 * @var \App\ViewModels\Program\ProgramItemViewModel $item
 */
?>
<div class="xl:hidden mx-1 sm:mx-2 p-4 sm:p-5 bg-white rounded-2xl flex flex-col gap-4"
     data-program-item-id="<?= htmlspecialchars((string) $item->programItemId) ?>"
     data-unit-price="<?= htmlspecialchars((string) $item->rawPrice) ?>"
     data-is-pay-what-you-like="<?= $item->isPayWhatYouLike ? '1' : '0' ?>">

    <!-- Top: Badge + Event Info + Delete -->
    <div class="flex gap-3 items-start">
        <!-- Event Type Badge -->
        <div class="w-16 sm:w-20 p-2 bg-green-100 rounded-xl flex flex-col items-center gap-1.5 flex-shrink-0">
            <img src="<?= htmlspecialchars($item->eventTypeImageUrl) ?>"
                 alt=""
                 class="w-10 sm:w-14 h-auto object-contain"
                 aria-hidden="true">
            <div class="w-full px-2 py-0.5 bg-white/80 rounded-lg flex justify-center">
                <span class="text-center text-green-800 text-[9px] sm:text-[10px] font-medium font-['Montserrat'] leading-4">
                    <?= htmlspecialchars($item->eventTypeLabel) ?>
                </span>
            </div>
        </div>

        <!-- Event Details -->
        <div class="flex-1 flex flex-col gap-1.5 min-w-0">
            <h3 class="text-slate-800 text-sm font-bold font-['Montserrat'] leading-4">
                <?= htmlspecialchars($item->eventTitle) ?>
            </h3>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span class="text-slate-800 text-xs font-light font-['Montserrat'] leading-3 truncate">
                    <?= htmlspecialchars($item->locationDisplay) ?>
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span class="text-slate-800 text-xs font-light font-['Montserrat'] leading-3">
                    <?= htmlspecialchars($item->dateTimeDisplay) ?>
                </span>
            </div>
            <?php if ($item->languageLabel !== null || $item->ageLabel !== null): ?>
                <div class="flex items-center gap-4 flex-wrap">
                    <?php if ($item->languageLabel !== null): ?>
                        <span class="text-slate-800 text-xs leading-3">
                            <span class="font-medium font-['Montserrat']">Language:</span>
                            <span class="font-light font-['Montserrat']"> <?= htmlspecialchars($item->languageLabel) ?></span>
                        </span>
                    <?php endif; ?>
                    <?php if ($item->ageLabel !== null): ?>
                        <span class="text-slate-800 text-xs leading-3">
                            <span class="font-medium font-['Montserrat']">Age:</span>
                            <span class="font-light font-['Montserrat']"> <?= htmlspecialchars($item->ageLabel) ?></span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Delete Button -->
        <button type="button"
                class="js-remove-item w-8 h-8 p-2 bg-rose-50 rounded-[10px] outline outline-2 outline-offset-[-2px] outline-red-200
                       flex items-center justify-center hover:bg-rose-100 transition-colors overflow-hidden flex-shrink-0"
                aria-label="Remove item">
            <svg class="w-4 h-4 text-red-400" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
        </button>
    </div>

    <!-- Bottom: Price / Quantity / Donation / Sum row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
        <!-- Price -->
        <div class="flex flex-col gap-1">
            <span class="text-slate-500 text-[10px] font-light font-['Montserrat'] uppercase">Price</span>
            <span class="p-2 bg-neutral-100 rounded-lg text-slate-800 text-xs font-normal font-['Montserrat'] text-center">
                <?= htmlspecialchars($item->priceDisplay) ?>
            </span>
        </div>

        <!-- Quantity -->
        <div class="flex flex-col gap-1">
            <span class="text-slate-500 text-[10px] font-light font-['Montserrat'] uppercase">Quantity</span>
            <div class="p-2 bg-stone-100 rounded-lg flex justify-center items-center gap-2.5">
                <?php if (!$item->isReservation): ?>
                <button type="button"
                        class="js-qty-decrease w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                        aria-label="Decrease quantity">
                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
                <?php endif; ?>
                <span class="js-qty-value text-neutral-600 text-sm font-normal font-['Inter']">
                    <?= htmlspecialchars((string) $item->quantity) ?>
                </span>
                <?php if (!$item->isReservation): ?>
                <button type="button"
                        class="js-qty-increase w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                        aria-label="Increase quantity">
                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Donation -->
        <?php if ($item->isPayWhatYouLike): ?>
            <div class="flex flex-col gap-1">
                <span class="text-slate-500 text-[10px] font-light font-['Montserrat'] uppercase">Donation</span>
                <div class="p-2 bg-green-100 rounded-lg flex items-center gap-1">
                    <span class="text-slate-800 text-xs font-normal font-['Montserrat']">€</span>
                    <input type="number"
                           class="js-donation-input w-full bg-transparent text-slate-800 text-xs font-normal font-['Montserrat']
                                  outline-none border-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                           value="<?= htmlspecialchars(number_format($item->donationAmount, 2, '.', '')) ?>"
                           min="0"
                           step="0.01"
                           aria-label="Donation amount">
                </div>
            </div>
        <?php endif; ?>

        <!-- Sum -->
        <div class="flex flex-col gap-1">
            <span class="text-slate-500 text-[10px] font-light font-['Montserrat'] uppercase">Sum</span>
            <span class="js-sum-display p-2 bg-violet-100 rounded-lg text-slate-800 text-xs font-normal font-['Montserrat'] text-center">
                <span><?= htmlspecialchars($item->sumDisplay) ?></span>
            </span>
        </div>
    </div>
</div>

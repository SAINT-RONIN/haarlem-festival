<?php
/**
 * My-Program single event card — desktop layout (xl+ breakpoint).
 *
 * @var \App\ViewModels\Program\ProgramItemViewModel $item
 */
?>
<div class="hidden xl:grid mx-2 px-6 py-6 bg-white rounded-2xl grid-cols-[6rem_1fr_5rem_6rem_6rem_5rem_2.25rem] gap-4 items-center"
     data-program-item-id="<?= htmlspecialchars((string) $item->programItemId) ?>"
     data-unit-price="<?= htmlspecialchars((string) $item->rawPrice) ?>"
     data-is-pay-what-you-like="<?= $item->isPayWhatYouLike ? '1' : '0' ?>">

    <!-- Col 1: Event Type Badge -->
    <div class="w-24 p-2.5 bg-green-100 rounded-2xl inline-flex flex-col justify-start items-center gap-2.5">
        <img src="<?= htmlspecialchars($item->eventTypeImageUrl) ?>"
             alt=""
             class="w-16 h-auto object-contain"
             aria-hidden="true">
        <div class="w-full px-3 py-1 bg-white/80 rounded-[10px] inline-flex justify-center items-center">
            <span class="text-center text-green-800 text-[10px] font-medium font-['Montserrat'] leading-5">
                <?= htmlspecialchars($item->eventTypeLabel) ?>
            </span>
        </div>
    </div>

    <!-- Col 2: Event Info -->
    <div class="p-2.5 flex flex-col gap-2.5 overflow-hidden">
        <h3 class="text-slate-800 text-xs font-bold font-['Montserrat'] leading-3">
            <?= htmlspecialchars($item->eventTitle) ?>
        </h3>
        <div class="flex flex-col gap-2.5">
            <div class="inline-flex items-center gap-2.5">
                <div class="flex items-center gap-[5px]">
                    <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span class="text-black text-xs font-medium font-['Montserrat'] leading-3">Location:</span>
                </div>
                <span class="text-slate-800 text-xs font-light font-['Montserrat'] leading-3">
                    <?= htmlspecialchars($item->locationDisplay) ?>
                </span>
            </div>
            <div class="inline-flex items-center gap-2.5">
                <div class="flex items-center gap-[5px]">
                    <svg class="w-4 h-4 text-slate-800 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                         aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span class="text-black text-xs font-medium font-['Montserrat'] leading-3">Date & time:</span>
                </div>
                <span class="text-slate-800 text-xs font-light font-['Montserrat'] leading-3">
                    <?= htmlspecialchars($item->dateTimeDisplay) ?>
                </span>
            </div>
        </div>
        <div class="inline-flex items-center gap-9">
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
    </div>

    <!-- Col 3: Price -->
    <div>
        <span class="w-full p-2.5 bg-neutral-100 rounded-[10px] inline-flex items-center gap-2.5">
            <span class="text-right text-slate-800 text-xs font-normal font-['Montserrat'] leading-3">
                <?= htmlspecialchars($item->priceDisplay) ?>
            </span>
        </span>
    </div>

    <!-- Col 4: Quantity Counter -->
    <div class="p-2.5 bg-stone-100 rounded-[10px] inline-flex justify-center items-center gap-2.5">
        <div class="flex-1 flex justify-center items-center gap-2.5">
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
            <span class="js-qty-value flex-1 text-center text-neutral-600 text-sm font-normal font-['Inter']">
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

    <!-- Col 5: Donation -->
    <div>
        <?php if ($item->isPayWhatYouLike): ?>
            <div class="w-full p-2.5 bg-green-100 rounded-[10px] inline-flex items-center gap-1">
                <span class="text-slate-800 text-xs font-normal font-['Montserrat'] leading-3">€</span>
                <input type="number"
                       class="js-donation-input w-full bg-transparent text-slate-800 text-xs font-normal font-['Montserrat'] leading-3
                              outline-none border-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                       value="<?= htmlspecialchars(number_format($item->donationAmount, 2, '.', '')) ?>"
                       min="0"
                       step="0.01"
                       aria-label="Donation amount">
            </div>
        <?php endif; ?>
    </div>

    <!-- Col 6: Sum -->
    <div>
        <span class="js-sum-display w-full p-2.5 bg-violet-100 rounded-[10px] inline-flex items-center gap-2.5">
            <span class="text-right text-slate-800 text-xs font-normal font-['Montserrat'] leading-3">
                <?= htmlspecialchars($item->sumDisplay) ?>
            </span>
        </span>
    </div>

    <!-- Col 7: Delete Button -->
    <button type="button"
            class="js-remove-item w-9 h-9 p-2.5 bg-rose-50 rounded-[10px] outline outline-2 outline-offset-[-2px] outline-red-200
                   flex items-center justify-center hover:bg-rose-100 transition-colors overflow-hidden"
            aria-label="Remove item">
        <svg class="w-5 h-5 text-red-400" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             aria-hidden="true">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
    </button>
</div>

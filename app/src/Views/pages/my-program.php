<?php
/**
 * My Program page view.
 *
 * @var \App\ViewModels\Program\MyProgramPageViewModel $viewModel
 */
$currentPage = 'my-program';
$includeNav = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-[#F5F1EB] min-h-screen">
    <!-- Page Title -->
    <div class="px-4 sm:px-8 lg:px-24 pt-8 pb-2">
        <h1 class="text-slate-800 text-2xl sm:text-3xl lg:text-4xl font-bold font-['Montserrat']">
            <?= htmlspecialchars($viewModel->pageTitle) ?>
        </h1>
    </div>

    <!-- Two-Column Layout -->
    <div class="px-4 sm:px-8 lg:px-24 py-6 flex flex-col lg:flex-row gap-6 lg:gap-12 items-start">

        <!-- Left Column: Selected Events -->
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
                            class="w-7 h-7 p-2.5 bg-rose-50 rounded-[10px] outline outline-2 outline-offset-[-2px] outline-red-200
                                   flex items-center justify-center hover:bg-rose-100 transition-colors overflow-hidden"
                            aria-label="Clear program">
                        <svg class="w-4 h-4 text-red-400" viewBox="0 0 24 24" fill="none"
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
                    <!-- Desktop card (xl+): 7-column grid -->
                    <div class="hidden xl:grid mx-2 px-6 py-6 bg-white rounded-2xl grid-cols-[6rem_1fr_5rem_6rem_6rem_5rem_2.25rem] gap-4 items-center"
                         data-program-item-id="<?= htmlspecialchars((string)$item->programItemId) ?>"
                         data-unit-price="<?= htmlspecialchars((string)$item->rawPrice) ?>"
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
                                <button type="button"
                                        class="js-qty-decrease w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                                        aria-label="Decrease quantity">
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
                                <span class="js-qty-value flex-1 text-center text-neutral-600 text-sm font-normal font-['Inter']">
                                    <?= htmlspecialchars((string)$item->quantity) ?>
                                </span>
                                <button type="button"
                                        class="js-qty-increase w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                                        aria-label="Increase quantity">
                                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </button>
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

                    <!-- Mobile/Tablet card (<xl): stacked layout -->
                    <div class="xl:hidden mx-1 sm:mx-2 p-4 sm:p-5 bg-white rounded-2xl flex flex-col gap-4"
                         data-program-item-id="<?= htmlspecialchars((string)$item->programItemId) ?>"
                         data-unit-price="<?= htmlspecialchars((string)$item->rawPrice) ?>"
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
                                    <button type="button"
                                            class="js-qty-decrease w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                                            aria-label="Decrease quantity">
                                        <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
                                    <span class="js-qty-value text-neutral-600 text-sm font-normal font-['Inter']">
                                        <?= htmlspecialchars((string)$item->quantity) ?>
                                    </span>
                                    <button type="button"
                                            class="js-qty-increase w-4 h-4 bg-slate-800 rounded-[5px] flex items-center justify-center hover:bg-slate-700 transition-colors"
                                            aria-label="Increase quantity">
                                        <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                    </button>
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

        <!-- Right Column: Payment Overview -->
        <div class="w-full lg:w-96 lg:sticky lg:top-6">
            <div class="p-4 sm:p-5 bg-[#ECE6DD] rounded-3xl">
                <div class="bg-white rounded-3xl p-4 sm:p-6 flex flex-col gap-5">
                    <!-- Heading -->
                    <h2 class="text-gray-900 text-lg sm:text-xl font-normal font-['Montserrat'] leading-6">
                        <?= htmlspecialchars($viewModel->paymentOverviewHeading) ?>
                    </h2>

                    <!-- Subtotal -->
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">Subtotal</span>
                        <span class="js-subtotal text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($viewModel->subtotal) ?>
                        </span>
                    </div>

                    <!-- Tax -->
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($viewModel->taxLabel) ?>
                        </span>
                        <span class="js-tax text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($viewModel->taxAmount) ?>
                        </span>
                    </div>

                    <!-- Divider -->
                    <div class="h-0 outline outline-2 outline-offset-[-1px] outline-neutral-700"></div>

                    <!-- Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">Total to pay</span>
                        <span class="js-total text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($viewModel->total) ?>
                        </span>
                    </div>

                    <!-- Checkout Button -->
                    <button type="button"
                            id="js-checkout-btn"
                            <?= $viewModel->canCheckout ? '' : 'disabled' ?>
                            class="w-full h-12 sm:h-14 rounded-[10px] inline-flex justify-center items-center gap-2
                                   transition-colors duration-200
                                   <?= $viewModel->canCheckout
                                       ? 'bg-green-600 hover:bg-green-700 cursor-pointer'
                                       : 'bg-gray-400 cursor-not-allowed' ?>">
                        <span class="text-center text-sm sm:text-base font-normal font-['Arial'] uppercase leading-6 tracking-wide
                                     <?= $viewModel->canCheckout ? 'text-white' : 'text-gray-500' ?>">
                            <?= htmlspecialchars($viewModel->checkoutButtonText) ?>
                        </span>
                        <svg class="w-5 h-5 <?= $viewModel->canCheckout ? 'text-white' : 'text-gray-500' ?>"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
<script src="/assets/js/my-program.js"></script>

<?php
/**
 * Shared main content for checkout page.
 *
 * @var \App\ViewModels\Program\CheckoutPageViewModel $viewModel
 */
?>
<main class="w-full bg-[#F5F1EB] min-h-screen">
    <!-- Page Title -->
    <div class="px-4 sm:px-8 lg:px-24 pt-8 pb-2">
        <h1 class="text-black text-3xl sm:text-4xl lg:text-6xl font-bold font-['Montserrat'] leading-tight">
            <?= htmlspecialchars($viewModel->pageTitle) ?>
        </h1>
    </div>

    <!-- Two-Column Layout -->
    <div class="px-4 sm:px-8 lg:px-24 py-6 flex flex-col lg:flex-row gap-6 lg:gap-12 items-start">

        <!-- Left Column: Payment Overview -->
        <div class="w-full lg:flex-1 p-4 sm:p-5 bg-[#ECE6DD] rounded-3xl flex flex-col gap-6">

            <!-- Back Button -->
            <a href="/my-program"
               class="self-stretch p-3.5 bg-slate-800 rounded-2xl inline-flex justify-center items-center
                      hover:bg-slate-700 transition-colors duration-200">
                <svg class="w-5 h-5 text-stone-100" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span class="text-stone-100 text-base sm:text-xl font-normal font-['Montserrat'] leading-5">
                    <?= htmlspecialchars($viewModel->backButtonText) ?>
                </span>
            </a>

            <!-- Payment Overview Card -->
            <div class="p-4 sm:p-6 bg-white rounded-3xl flex flex-col gap-10 overflow-hidden">

                <!-- Items Section -->
                <div class="flex flex-col gap-5">
                    <h2 class="text-gray-900 text-lg sm:text-xl font-normal font-['Montserrat'] leading-6">
                        <?= htmlspecialchars($viewModel->paymentOverviewHeading) ?>
                    </h2>

                    <!-- Items Grid -->
                    <div class="grid grid-cols-[auto_auto_1fr_auto_auto] gap-x-3 gap-y-1 items-baseline">
                        <?php foreach ($viewModel->items as $item): ?>
                            <span class="text-gray-900 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                                <?= htmlspecialchars($item->quantityDisplay) ?>
                            </span>
                            <span class="text-gray-900 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">·</span>
                            <span class="text-gray-900 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                                <?= htmlspecialchars($item->eventTitle) ?>
                            </span>
                            <span class="text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6 text-right">€</span>
                            <span class="text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6 text-right">
                                <?= htmlspecialchars(ltrim($item->priceDisplay, '€')) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="flex flex-col gap-3.5">
                    <div class="flex flex-col gap-2.5">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">Subtotal</span>
                            <span class="text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                                <?= htmlspecialchars($viewModel->subtotal) ?>
                            </span>
                        </div>
                        <!-- Tax -->
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">
                                <?= htmlspecialchars($viewModel->taxLabel) ?>
                            </span>
                            <span class="text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                                <?= htmlspecialchars($viewModel->taxAmount) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-0 outline outline-2 outline-offset-[-1px] outline-neutral-700"></div>

                    <!-- Total -->
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-700 text-sm sm:text-base font-bold font-['Montserrat'] leading-6">Total to pay</span>
                        <span class="text-neutral-700 text-sm sm:text-base font-normal font-['Montserrat'] leading-6">
                            <?= htmlspecialchars($viewModel->total) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Personal Info + Payment Methods -->
        <div class="w-full lg:flex-1 flex flex-col gap-5">
            <div class="p-4 sm:p-5 bg-[#ECE6DD] rounded-3xl flex flex-col gap-6">

                <!-- Personal Information -->
                <div class="px-3 py-3.5 bg-white rounded-3xl outline outline-2 outline-offset-[-2px] outline-gray-200 flex flex-col gap-3.5">
                    <?php if (!$viewModel->isLoggedIn): ?>
                        <div class="p-3 rounded-2xl bg-amber-50 text-amber-800 text-sm sm:text-base font-['Montserrat'] leading-5">
                            Please <a href="/login" class="underline font-semibold">log in</a> to complete payment.
                        </div>
                    <?php endif; ?>

                    <div class="flex items-start gap-3">
                        <svg class="w-10 h-10 text-gray-600 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <div class="flex-1 flex flex-col">
                            <span class="text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-5">
                                <?= htmlspecialchars($viewModel->personalInfoHeading) ?>
                            </span>
                            <span class="text-gray-500 text-xs sm:text-sm font-normal font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($viewModel->personalInfoSubtext) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <form id="js-checkout-form" class="flex flex-col gap-3.5">
                        <!-- First Name -->
                        <div class="p-3 sm:p-3.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200 flex flex-col gap-[5px]">
                            <label class="text-gray-500 text-base sm:text-lg font-normal font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($viewModel->firstNameLabel) ?>
                            </label>
                            <input type="text"
                                   name="firstName"
                                   placeholder="<?= htmlspecialchars($viewModel->firstNamePlaceholder) ?>"
                                   required
                                   class="w-full p-2.5 bg-white rounded-[10px] outline outline-1 outline-offset-[-1px] outline-zinc-300
                                          text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-4
                                          placeholder:text-gray-400 focus:outline-2 focus:outline-slate-800 transition-colors">
                        </div>

                        <!-- Last Name -->
                        <div class="p-3 sm:p-3.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200 flex flex-col gap-[5px]">
                            <label class="text-gray-500 text-base sm:text-lg font-normal font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($viewModel->lastNameLabel) ?>
                            </label>
                            <input type="text"
                                   name="lastName"
                                   placeholder="<?= htmlspecialchars($viewModel->lastNamePlaceholder) ?>"
                                   required
                                   class="w-full p-2.5 bg-white rounded-[10px] outline outline-1 outline-offset-[-1px] outline-zinc-300
                                          text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-4
                                          placeholder:text-gray-400 focus:outline-2 focus:outline-slate-800 transition-colors">
                        </div>

                        <!-- Email -->
                        <div class="p-3 sm:p-3.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200 flex flex-col gap-[5px]">
                            <label class="text-gray-500 text-base sm:text-lg font-normal font-['Montserrat'] leading-4">
                                <?= htmlspecialchars($viewModel->emailLabel) ?>
                            </label>
                            <input type="email"
                                   name="email"
                                   placeholder="<?= htmlspecialchars($viewModel->emailPlaceholder) ?>"
                                   required
                                   class="w-full p-2.5 bg-white rounded-[10px] outline outline-1 outline-offset-[-1px] outline-zinc-300
                                          text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-4
                                          placeholder:text-gray-400 focus:outline-2 focus:outline-slate-800 transition-colors">
                        </div>
                    </form>
                </div>

                <!-- Payment Methods -->
                <div class="p-4 sm:p-6 bg-white rounded-3xl flex flex-col gap-4">
                    <h2 class="pl-2.5 text-gray-900 text-lg sm:text-xl font-normal font-['Montserrat'] leading-6">
                        <?= htmlspecialchars($viewModel->paymentMethodsHeading) ?>
                    </h2>

                    <div class="p-2.5 flex flex-col gap-6">
                        <!-- Payment Options -->
                        <div class="px-3 sm:px-3.5 py-3.5 rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-300 flex flex-col gap-3.5">
                            <!-- Credit Card -->
                            <label class="px-3 py-2.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200
                                          flex items-center gap-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <svg class="w-7 h-7 text-gray-600 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                                <div class="flex-1 flex flex-col gap-0.5">
                                    <span class="text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-5">Credit Card</span>
                                    <span class="text-gray-500 text-xs sm:text-sm font-normal font-['Montserrat'] leading-4">Visa, Mastercard, American Express(Amex)</span>
                                </div>
                                <input type="radio" name="paymentMethod" value="credit_card" checked
                                       class="w-5 h-5 accent-slate-800">
                            </label>

                            <!-- iDEAL -->
                            <label class="px-3 py-2.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200
                                          flex items-center gap-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <svg class="w-7 h-7 text-gray-600 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <rect x="1" y="3" width="22" height="18" rx="2"></rect>
                                    <path d="M12 8v8"></path>
                                    <path d="M8 12h8"></path>
                                </svg>
                                <div class="flex-1 flex flex-col gap-0.5">
                                    <span class="text-gray-900 text-base font-normal font-['Montserrat'] leading-5">iDEAL</span>
                                    <span class="text-gray-500 text-xs sm:text-sm font-normal font-['Montserrat'] leading-4">Pay with your bank</span>
                                </div>
                                <input type="radio" name="paymentMethod" value="ideal"
                                       class="w-5 h-5 accent-slate-800">
                            </label>

                            <!-- PayPal -->
                            <p class="text-gray-500 text-xs sm:text-sm font-normal font-['Montserrat'] leading-4 px-1">
                                Powered by Stripe Checkout (card and iDEAL).
                            </p>
                        </div>

                        <!-- Save Details + Pay Button -->
                        <div class="p-3.5 rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-300 flex flex-col gap-3.5">
                            <div id="js-checkout-error"
                                 class="hidden p-3 rounded-xl bg-red-50 text-red-700 text-sm sm:text-base font-['Montserrat'] leading-5"></div>

                            <!-- Save Details Checkbox -->
                            <label class="p-2.5 bg-white rounded-2xl outline outline-2 outline-offset-[-2px] outline-gray-200
                                          flex items-center gap-3 cursor-pointer hover:bg-gray-50 transition-colors">
                                <svg class="w-7 h-7 text-gray-600 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                <div class="flex-1 flex flex-col gap-0.5">
                                    <span class="text-gray-900 text-base sm:text-lg font-normal font-['Montserrat'] leading-5">
                                        <?= htmlspecialchars($viewModel->saveDetailsLabel) ?>
                                    </span>
                                    <span class="text-gray-500 text-xs sm:text-sm font-normal font-['Montserrat'] leading-4">
                                        <?= htmlspecialchars($viewModel->saveDetailsSubtext) ?>
                                    </span>
                                </div>
                                <input type="checkbox" name="saveDetails" value="1"
                                       class="w-5 h-5 accent-slate-800 flex-shrink-0">
                            </label>

                            <!-- Pay Button -->
                            <button type="button"
                                    id="js-pay-btn"
                                    class="self-stretch h-12 sm:h-14 bg-slate-800 rounded-[10px] inline-flex justify-center items-center gap-2
                                           hover:bg-slate-700 transition-colors duration-200 cursor-pointer">
                                <svg class="w-4 h-4 text-stone-100" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <path d="M9 12l2 2 4-4"></path>
                                </svg>
                                <span class="text-center text-stone-100 text-sm sm:text-base font-normal font-['Arial'] uppercase leading-6 tracking-wide">
                                    <?= htmlspecialchars($viewModel->payButtonText) ?>
                                </span>
                                <svg class="w-5 h-5 text-stone-100" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


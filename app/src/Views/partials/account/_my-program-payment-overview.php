<?php
/**
 * My-Program right column: payment overview card with subtotal/tax/total and checkout button.
 *
 * @var \App\ViewModels\Program\MyProgramPageViewModel $viewModel
 */
?>
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

<?php
/**
 * Checkout retry page body — rendered inside the shared shell's <main>.
 *
 * @var \App\ViewModels\Program\CheckoutRetryViewModel $viewModel
 */
?>
<div class="px-4 sm:px-8 lg:px-24 py-8">
    <div class="max-w-xl mx-auto">
        <!-- Page Title -->
        <h1 class="text-slate-800 text-2xl sm:text-3xl font-bold font-['Montserrat'] mb-6">
            Complete Your Payment
        </h1>

        <?php if ($viewModel->isExpired): ?>
            <!-- Expired Order -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                        Expired
                    </span>
                </div>
                <p class="text-slate-600 mb-2">
                    The payment deadline for order <strong><?= htmlspecialchars($viewModel->orderNumber) ?></strong> has passed.
                </p>
                <p class="text-slate-500 text-sm mb-6">
                    Deadline was: <?= htmlspecialchars($viewModel->payBeforeFormatted) ?>
                </p>
                <a href="/my-program"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-royal-blue text-white rounded-xl font-medium hover:bg-red transition-colors">
                    Back to My Program
                </a>
            </div>
        <?php else: ?>
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Order Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-slate-500">Order Number</span>
                        <span class="text-sm font-medium text-slate-900"><?= htmlspecialchars($viewModel->orderNumber) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-slate-500">Total Amount</span>
                        <span class="text-lg font-bold text-slate-900"><?= htmlspecialchars($viewModel->totalAmountFormatted) ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-sm text-slate-500">Pay Before</span>
                        <span class="text-sm font-medium text-amber-600"><?= htmlspecialchars($viewModel->payBeforeFormatted) ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm mb-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-4">Payment Method</h2>
                <div class="grid grid-cols-1 gap-4">
                    <button type="button" data-method="credit_card"
                            class="js-payment-method p-4 border-2 border-gray-200 rounded-xl flex flex-col items-center gap-2 hover:border-slate-800 transition-colors cursor-pointer">
                        <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2" stroke-width="2"/>
                            <line x1="1" y1="10" x2="23" y2="10" stroke-width="2"/>
                        </svg>
                        <span class="text-sm font-medium text-slate-700">Stripe</span>
                    </button>
                </div>
            </div>

            <!-- Pay Button -->
            <button type="button" id="btn-retry-pay" disabled
                    class="w-full py-3 px-4 bg-gray-400 text-white rounded-2xl font-semibold cursor-not-allowed transition-colors flex items-center justify-center gap-2">
                Select a payment method
            </button>

            <!-- Error Message -->
            <div id="retry-error" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"></div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$viewModel->isExpired): ?>
<script>
    var RETRY_ORDER_ID = <?= $viewModel->orderId ?>;
</script>
<script src="/assets/js/checkout-retry.js"></script>
<?php endif; ?>

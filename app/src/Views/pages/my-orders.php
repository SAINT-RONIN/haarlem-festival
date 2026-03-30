<?php
/**
 * My Orders page view — displays the customer's order history with ticket downloads.
 *
 * @var \App\ViewModels\Program\MyOrdersViewModel $viewModel
 */
$currentPage = $viewModel->currentPage;
$includeNav = true;
$isLoggedIn = $viewModel->isLoggedIn;
?>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="w-full bg-[#F5F1EB] min-h-screen">
    <!-- Page Title -->
    <div class="px-4 sm:px-8 lg:px-24 pt-8 pb-2">
        <h1 class="text-slate-800 text-2xl sm:text-3xl lg:text-4xl font-bold font-['Montserrat']">
            My Orders
        </h1>
    </div>

    <div class="px-4 sm:px-8 lg:px-24 py-6">
        <?php if (empty($viewModel->orders)): ?>
            <!-- Empty State -->
            <div class="p-8 sm:p-12 bg-[#ECE6DD] rounded-2xl flex flex-col items-center gap-4 text-center">
                <svg class="w-16 h-16 text-slate-400" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     aria-hidden="true">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 01-8 0"></path>
                </svg>
                <p class="text-slate-600 text-lg font-normal font-['Montserrat']">
                    You haven't placed any orders yet.
                </p>
                <a href="/"
                   class="mt-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white rounded-lg font-['Montserrat'] text-sm transition-colors duration-200">
                    Browse Events
                </a>
            </div>
        <?php else: ?>
            <!-- Order Cards -->
            <div class="flex flex-col gap-6">
                <?php foreach ($viewModel->orders as $order): ?>
                    <div class="p-5 sm:p-6 bg-[#ECE6DD] rounded-2xl flex flex-col gap-4">
                        <!-- Order Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-slate-800 text-lg sm:text-xl font-bold font-['Montserrat']">
                                    Order #<?= htmlspecialchars($order->orderNumber) ?>
                                </h2>
                                <span class="text-slate-500 text-sm font-normal font-['Montserrat']">
                                    <?= htmlspecialchars($order->createdAtFormatted) ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1 rounded-full text-xs font-medium font-['Montserrat'] <?= htmlspecialchars($order->statusBadgeClass) ?>">
                                    <?= htmlspecialchars($order->statusText) ?>
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-medium font-['Montserrat'] <?= htmlspecialchars($order->paymentBadgeClass) ?>">
                                    Payment: <?= htmlspecialchars($order->paymentStatusText) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="p-4 bg-white rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-slate-500 text-xs font-normal font-['Montserrat'] uppercase">Items</span>
                                    <span class="text-slate-800 text-base font-medium font-['Montserrat']">
                                        <?= htmlspecialchars((string) $order->itemCount) ?>
                                    </span>
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-slate-500 text-xs font-normal font-['Montserrat'] uppercase">Total</span>
                                    <span class="text-slate-800 text-base font-bold font-['Montserrat']">
                                        <?= htmlspecialchars($order->totalAmountFormatted) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 flex-wrap">
                                <?php if ($order->canRetryPayment): ?>
                                    <a href="<?= htmlspecialchars($order->retryUrl) ?>"
                                       class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium font-['Montserrat'] transition-colors duration-200 inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                             aria-hidden="true">
                                            <polyline points="23 4 23 10 17 10"></polyline>
                                            <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"></path>
                                        </svg>
                                        Complete Payment
                                    </a>
                                <?php elseif ($order->statusText === 'Expired' || $order->statusText === 'Cancelled'): ?>
                                    <a href="/my-program"
                                       class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-sm font-medium font-['Montserrat'] transition-colors duration-200 inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                             aria-hidden="true">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"></path>
                                        </svg>
                                        Re-order
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($order->ticketPdfUrls)): ?>
                            <!-- Ticket Downloads -->
                            <div class="p-4 bg-white rounded-2xl flex flex-col gap-3">
                                <h3 class="text-slate-800 text-sm font-bold font-['Montserrat'] uppercase">
                                    Download Tickets
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($order->ticketPdfUrls as $ticket): ?>
                                        <a href="<?= htmlspecialchars($ticket['url']) ?>"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-normal font-['Montserrat'] transition-colors duration-200 inline-flex items-center gap-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 aria-hidden="true">
                                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                            <?= htmlspecialchars($ticket['ticketCode']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

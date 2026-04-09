<?php
/**
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\Helpers\CmsOrderViewHelper;
use App\Helpers\FormatHelper;

?>
        <!-- Back Link -->
        <a href="/cms/orders" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 mb-4">
            &larr; Back to Orders
        </a>

        <!-- Header -->
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Order <?= htmlspecialchars($viewModel->order->orderNumber) ?>
            </h1>
        </header>

        <!-- Order Header Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Order Info</h3>
                    <p class="text-sm text-gray-900"><span class="font-medium">Number:</span> <?= htmlspecialchars($viewModel->order->orderNumber) ?></p>
                    <p class="text-sm text-gray-900 mt-1">
                        <span class="font-medium">Status:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= htmlspecialchars(CmsOrderViewHelper::resolveOrderBadgeClass($viewModel->order->status)) ?>">
                            <?= htmlspecialchars($viewModel->order->status) ?>
                        </span>
                    </p>
                    <p class="text-sm text-gray-900 mt-1"><span class="font-medium">Created:</span> <?= htmlspecialchars(CmsOrderViewHelper::formatUtcDate($viewModel->order->createdAtUtc)) ?></p>
                    <?php if ($viewModel->order->payBeforeUtc !== ''): ?>
                        <p class="text-sm text-gray-900 mt-1"><span class="font-medium">Pay Before:</span> <?= htmlspecialchars(CmsOrderViewHelper::formatUtcDate($viewModel->order->payBeforeUtc)) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Recipient</h3>
                    <p class="text-sm text-gray-900">
                        <?= htmlspecialchars(trim($viewModel->order->ticketRecipientFirstName . ' ' . $viewModel->order->ticketRecipientLastName)) ?: 'N/A' ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($viewModel->order->ticketRecipientEmail ?: $viewModel->order->userEmail ?: 'N/A') ?></p>
                    <?php if ($viewModel->order->ticketEmailSentAtUtc !== null): ?>
                        <p class="text-sm text-green-600 mt-1">Ticket email sent: <?= htmlspecialchars(CmsOrderViewHelper::formatUtcDate($viewModel->order->ticketEmailSentAtUtc)) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Totals</h3>
                    <p class="text-sm text-gray-900"><span class="font-medium">Subtotal:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->order->subtotal)) ?></p>
                    <p class="text-sm text-gray-900 mt-1"><span class="font-medium">VAT:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->order->vatTotal)) ?></p>
                    <p class="text-lg font-bold text-gray-900 mt-2"><span class="font-medium">Total:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->order->totalAmount)) ?></p>
                </div>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Line Items</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event / Pass</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line Total</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->items)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">No line items found for this order.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($viewModel->items as $item): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($item->eventTitle ?? $item->passName ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($item->venueName ?? 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $item->sessionDateTime !== null ? htmlspecialchars(CmsOrderViewHelper::formatUtcDate($item->sessionDateTime)) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $item->quantity ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars(FormatHelper::price($item->unitPrice)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars(number_format($item->vatRate * 100, 0)) ?>%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars(FormatHelper::price($item->donationAmount)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= htmlspecialchars(FormatHelper::price($item->lineTotal)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Payments Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Payments</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider Ref</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid At</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->payments)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No payments found for this order.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($viewModel->payments as $payment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($payment->method ?: 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= htmlspecialchars(CmsOrderViewHelper::resolvePaymentBadgeClass($payment->status)) ?>">
                                    <?= htmlspecialchars($payment->status) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($payment->providerRef ?: 'N/A') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars(CmsOrderViewHelper::formatUtcDate($payment->createdAtUtc)) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $payment->paidAtUtc !== null ? htmlspecialchars(CmsOrderViewHelper::formatUtcDate($payment->paidAtUtc)) : 'N/A' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Invoice</h2>
            </div>
            <?php if ($viewModel->invoice !== null): ?>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">Invoice Number:</span> <?= htmlspecialchars($viewModel->invoice->invoiceNumber) ?></p>
                            <p class="text-sm text-gray-900 mt-1"><span class="font-medium">Date:</span> <?= htmlspecialchars($viewModel->invoice->invoiceDateUtc->format('d M Y, H:i')) ?></p>
                            <?php if ($viewModel->invoice->paymentDateUtc !== null): ?>
                                <p class="text-sm text-gray-900 mt-1"><span class="font-medium">Payment Date:</span> <?= htmlspecialchars($viewModel->invoice->paymentDateUtc->format('d M Y, H:i')) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">Client:</span> <?= htmlspecialchars($viewModel->invoice->clientName) ?></p>
                            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($viewModel->invoice->emailAddress) ?></p>
                            <?php if ($viewModel->invoice->addressLine !== ''): ?>
                                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($viewModel->invoice->addressLine) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900"><span class="font-medium">Subtotal:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->invoice->subtotalAmount)) ?></p>
                            <p class="text-sm text-gray-900 mt-1"><span class="font-medium">VAT:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->invoice->totalVatAmount)) ?></p>
                            <p class="text-sm font-bold text-gray-900 mt-1"><span class="font-medium">Total:</span> <?= htmlspecialchars(FormatHelper::price((float) $viewModel->invoice->totalAmount)) ?></p>
                            <?php if ($viewModel->invoicePdfPath !== null): ?>
                                <a href="<?= htmlspecialchars($viewModel->invoicePdfPath) ?>" target="_blank"
                                   class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                    <i data-lucide="download" class="w-4 h-4" aria-hidden="true"></i>
                                    Download PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-500">
                    <?php if ($viewModel->order->status === 'Paid'): ?>
                        Invoice is being generated...
                    <?php else: ?>
                        Invoice will be generated after payment.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tickets Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tickets</h2>
                <?php if ($viewModel->order->status === 'Paid'): ?>
                    <form method="POST" action="/cms/orders/<?= (int) $viewModel->order->orderId ?>/resend-tickets">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                            <i data-lucide="send" class="w-4 h-4" aria-hidden="true"></i>
                            Resend Ticket Email
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned At</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scanned By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PDF</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->tickets)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No tickets found for this order.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($viewModel->tickets as $ticket): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                <?= htmlspecialchars($ticket->ticketCode) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($ticket->isScanned): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Scanned</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Not Scanned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $ticket->scannedAtUtc !== null ? htmlspecialchars(CmsOrderViewHelper::formatUtcDate($ticket->scannedAtUtc)) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $ticket->scannedByUserName !== null ? htmlspecialchars($ticket->scannedByUserName) : 'N/A' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($ticket->pdfAssetPath !== null): ?>
                                    <a href="<?= htmlspecialchars($ticket->pdfAssetPath) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">Download</a>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

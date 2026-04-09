<?php
/**
 * Order info + recipient + totals card.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\Helpers\CmsOrderViewHelper;
use App\Helpers\FormatHelper;
?>
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

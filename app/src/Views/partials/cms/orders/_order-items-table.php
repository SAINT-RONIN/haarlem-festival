<?php
/**
 * Order line-items table.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\Helpers\CmsOrderViewHelper;
use App\Helpers\FormatHelper;
?>
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

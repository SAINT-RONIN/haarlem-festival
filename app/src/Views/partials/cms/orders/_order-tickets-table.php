<?php
/**
 * Order tickets table with scan status.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\Helpers\CmsOrderViewHelper;
?>
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

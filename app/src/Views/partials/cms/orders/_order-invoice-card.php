<?php
/**
 * Invoice details card.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\Helpers\FormatHelper;
?>
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

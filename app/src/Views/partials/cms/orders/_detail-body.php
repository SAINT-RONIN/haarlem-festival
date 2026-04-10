<?php
/**
 * CMS order detail body — assembles the five section cards.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\View\ViewRenderer;
?>
        <!-- Back Link -->
        <a href="/cms/orders" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 mb-4">
            &larr; Back to Orders
        </a>

        <!-- Header -->
        <header class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">
                Order <?= htmlspecialchars($viewModel->order->orderNumber) ?>
            </h1>
            <div class="flex gap-2">
                <a href="/cms/orders/<?= $viewModel->order->orderId ?>/export/csv"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4" aria-hidden="true"></i>
                    Export CSV
                </a>
                <a href="/cms/orders/<?= $viewModel->order->orderId ?>/export/excel"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i data-lucide="table" class="w-4 h-4" aria-hidden="true"></i>
                    Export Excel
                </a>
            </div>
        </header>

        <?php ViewRenderer::render(__DIR__ . '/_order-summary-card.php',    ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-items-table.php',     ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-payments-table.php',  ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-invoice-card.php',    ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-tickets-table.php',   ['viewModel' => $viewModel]); ?>

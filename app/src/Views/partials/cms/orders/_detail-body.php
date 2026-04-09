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
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Order <?= htmlspecialchars($viewModel->order->orderNumber) ?>
            </h1>
        </header>

        <?php ViewRenderer::render(__DIR__ . '/_order-summary-card.php',    ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-items-table.php',     ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-payments-table.php',  ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-invoice-card.php',    ['viewModel' => $viewModel]); ?>
        <?php ViewRenderer::render(__DIR__ . '/_order-tickets-table.php',   ['viewModel' => $viewModel]); ?>

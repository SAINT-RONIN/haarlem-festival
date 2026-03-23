<?php
/**
 * CMS Orders list page.
 *
 * @var string $currentView
 * @var \App\ViewModels\Cms\CmsOrdersListViewModel $viewModel
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Haarlem CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="flex min-h-screen">
    <?php require __DIR__ . '/../../partials/cms/sidebar.php'; ?>

    <main class="flex-1 p-8 overflow-auto">
        <!-- Header -->
        <header class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Orders Management</h1>
                    <p class="text-gray-600 mt-1">View all customer orders and payment statuses</p>
                </div>
            </div>
        </header>

        <?php require __DIR__ . '/../../partials/cms/_flash-messages.php'; ?>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="/cms/orders" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                    <select name="status" id="status"
                            class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                        <option value="">All Statuses</option>
                        <?php foreach (['Pending', 'Paid', 'Cancelled', 'Expired', 'Refunded'] as $statusOption): ?>
                            <option value="<?= htmlspecialchars($statusOption) ?>"
                                    <?= $viewModel->selectedStatus === $statusOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars($statusOption) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                    Apply Filters
                </button>
                <?php if (!empty($viewModel->selectedStatus)): ?>
                    <a href="/cms/orders" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Clear Filters
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">All Orders</h2>
                    <p class="text-sm text-gray-500"><?= count($viewModel->orders) ?> order(s) found</p>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order #
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Items
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Order Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->orders)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <p>No orders found</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($viewModel->orders as $order): ?>
                        <?php /** @var \App\ViewModels\Cms\CmsOrderListItemViewModel $order */ ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($order->orderNumber) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars((string) $order->userAccountId) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($order->userEmail) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs">
                                    <?= htmlspecialchars($order->itemsSummary) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($order->totalAmount) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= htmlspecialchars($order->statusBadgeClass) ?>">
                                    <?= htmlspecialchars($order->orderStatus) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= htmlspecialchars($order->paymentBadgeClass) ?>">
                                    <?= htmlspecialchars($order->paymentStatus) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= htmlspecialchars($order->createdAt) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="/assets/js/cms/cms-common.js"></script>
</body>
</html>

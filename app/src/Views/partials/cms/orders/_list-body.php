<?php
/**
 * @var \App\ViewModels\Cms\CmsOrdersListViewModel $viewModel
 */
?>
        <?php \App\View\ViewRenderer::render(__DIR__ . '/../_list-page-header.php', [
            'title'    => 'Orders Management',
            'subtitle' => 'View all customer orders and payment statuses',
        ]); ?>

        <!--
            Single form shared by filtering and exporting. Status + date range are real
            inputs, so they travel with every submit: "Apply Filters" submits to the list
            (default action), while the export buttons override the target via formaction.
            The export endpoints stream the whole filtered range, ignoring pagination.
        -->
        <form method="GET" action="/cms/orders">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                        <select name="status" id="status"
                                class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                            <option value="">All Statuses</option>
                            <?php foreach ($viewModel->statusOptions as $statusValue): ?>
                                <option value="<?= htmlspecialchars($statusValue) ?>"
                                        <?= $viewModel->selectedStatus === $statusValue ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($statusValue) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <input type="date" name="from" id="from" value="<?= htmlspecialchars($viewModel->fromDate) ?>"
                               class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>
                    <div>
                        <label for="to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input type="date" name="to" id="to" value="<?= htmlspecialchars($viewModel->toDate) ?>"
                               class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 py-2 px-3 border">
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                        Apply Filters
                    </button>
                    <a href="/cms/orders" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Reset to This Week
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Orders</h2>
                        <p class="text-sm text-gray-500">
                            <?= $viewModel->totalCount ?> order(s) in range
                            &middot; page <?= $viewModel->currentPage ?> of <?= $viewModel->totalPages ?>
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 max-w-2xl">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Columns:</span>
                            <?php foreach (\App\Export\OrderExportColumns::catalog() as $columnKey => $column): ?>
                                <label class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                                    <input type="checkbox" name="columns[]" value="<?= htmlspecialchars($columnKey) ?>" checked
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <?= htmlspecialchars($column['label']) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" formaction="/cms/orders/export/csv"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4" aria-hidden="true"></i>
                                Export CSV
                            </button>
                            <button type="submit" formaction="/cms/orders/export/excel"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <i data-lucide="table" class="w-4 h-4" aria-hidden="true"></i>
                                Export Excel
                            </button>
                        </div>
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
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($viewModel->orders)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="/cms/orders/<?= $order->orderId ?>" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>

            <?php
                // Preserve the active filter on page links so Prev/Next keep the same range.
                $pageBaseQuery = [
                    'status' => $viewModel->selectedStatus,
                    'from'   => $viewModel->fromDate,
                    'to'     => $viewModel->toDate,
                ];
                $pageUrl = static function (int $page) use ($pageBaseQuery): string {
                    return '/cms/orders?' . http_build_query($pageBaseQuery + ['page' => $page]);
                };
            ?>
            <?php if ($viewModel->totalPages > 1): ?>
                <div class="px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        Page <?= $viewModel->currentPage ?> of <?= $viewModel->totalPages ?>
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($viewModel->currentPage > 1): ?>
                            <a href="<?= htmlspecialchars($pageUrl($viewModel->currentPage - 1)) ?>"
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-2 text-sm border border-gray-200 rounded-md text-gray-300 cursor-not-allowed">Previous</span>
                        <?php endif; ?>
                        <?php if ($viewModel->currentPage < $viewModel->totalPages): ?>
                            <a href="<?= htmlspecialchars($pageUrl($viewModel->currentPage + 1)) ?>"
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Next
                            </a>
                        <?php else: ?>
                            <span class="px-3 py-2 text-sm border border-gray-200 rounded-md text-gray-300 cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </form>

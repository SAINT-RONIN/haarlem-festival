<?php if (!empty($viewModel->successMessage)): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
        <?= htmlspecialchars($viewModel->successMessage) ?>
    </div>
<?php endif; ?>
<?php if (!empty($viewModel->errorMessage)): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
        <?= htmlspecialchars($viewModel->errorMessage) ?>
    </div>
<?php endif; ?>

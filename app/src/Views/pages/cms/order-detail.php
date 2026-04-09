<?php
/**
 * CMS Order detail page.
 *
 * @var \App\ViewModels\Cms\CmsOrderDetailViewModel $viewModel
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$layout = new CmsPageLayout(
    title: 'Order ' . htmlspecialchars($viewModel->order->orderNumber) . ' - Haarlem CMS',
    currentView: 'orders',
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/orders/_detail-body.php',
        ['viewModel' => $viewModel],
    ),
    includeFlashMessages: true,
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => $viewModel,
]); ?>

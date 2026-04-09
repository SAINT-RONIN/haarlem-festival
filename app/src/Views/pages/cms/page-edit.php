<?php
/**
 * CMS Page Edit - Admin page content editor.
 *
 * @var \App\ViewModels\Cms\CmsPageInfoViewModel $page
 * @var \App\ViewModels\Cms\CmsSectionDisplayViewModel[] $sections
 * @var \App\ViewModels\Cms\CmsJazzLineupManagerViewModel|null $jazzLineupManager
 * @var array{HEADING: int, TEXT: int, HTML: int, BUTTON_TEXT: int} $contentLimits
 * @var \App\ViewModels\Cms\CmsImageLimitsViewModel $imageLimits
 * @var string|null $successMessage
 * @var string|null $errorMessage
 * @var string $userName
 */

use App\View\CmsPageLayout;
use App\View\ViewRenderer;
use App\View\ViewTemplate;

$currentView = 'pages';
$userName ??= 'Administrator';

$layout = new CmsPageLayout(
    title: 'Edit ' . htmlspecialchars($page->title) . ' | CMS',
    currentView: $currentView,
    content: new ViewTemplate(
        __DIR__ . '/../../partials/cms/pages/_edit-body.php',
        [
            'page'             => $page,
            'sections'         => $sections ?? [],
            'jazzLineupManager'=> $jazzLineupManager ?? null,
            'contentLimits'    => $contentLimits ?? [],
            'imageLimits'      => $imageLimits ?? [],
            'successMessage'   => $successMessage ?? null,
            'errorMessage'     => $errorMessage ?? null,
            'userName'         => $userName,
            'csrfToken'        => $csrfToken ?? '',
            'previewUrl'       => $previewUrl ?? null,
        ],
    ),
    includeFlashMessages: false,
    mainClass: 'flex-1 flex flex-col overflow-hidden',
    extraScripts: [
        'https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js',
        '/assets/js/cms/page-edit.js',
    ],
);
?>
<?php ViewRenderer::render(__DIR__ . '/../../partials/cms/_shell.php', [
    'layout'    => $layout,
    'viewModel' => null,
]); ?>

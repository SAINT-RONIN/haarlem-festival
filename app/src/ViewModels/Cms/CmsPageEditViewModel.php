<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS page editor.
 */
final readonly class CmsPageEditViewModel
{
    /**
     * @param CmsSectionDisplayViewModel[]                        $sections
     * @param array{HEADING: int, TEXT: int, HTML: int, BUTTON_TEXT: int} $contentLimits
     */
    public function __construct(
        public CmsPageInfoViewModel    $page,
        public array                   $sections,
        public array                   $contentLimits,
        public CmsImageLimitsViewModel $imageLimits,
    ) {}
}

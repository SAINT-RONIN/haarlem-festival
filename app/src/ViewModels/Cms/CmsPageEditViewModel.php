<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * View data for the CMS page content editor.
 *
 * Carries page info, editable sections, and content/image limit configs.
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

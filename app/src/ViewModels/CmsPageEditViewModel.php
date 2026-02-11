<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Utils\CmsContentLimits;

/**
 * ViewModel for the CMS page edit view.
 *
 * Shapes page, section, and item data for display in the editor.
 */
class CmsPageEditViewModel
{
    private array $pageData;

    public function __construct(array $pageData)
    {
        $this->pageData = $pageData;
    }

    /**
     * Gets all view data for the template.
     */
    public function getViewData(): array
    {
        return [
            'page' => $this->formatPage(),
            'sections' => $this->formatSections(),
            'contentLimits' => $this->getContentLimits(),
            'imageLimits' => $this->getImageLimits()
        ];
    }

    /**
     * Formats page data for display.
     */
    private function formatPage(): array
    {
        $page = $this->pageData['page'];
        return [
            'id' => $page['CmsPageId'],
            'title' => $page['Title'],
            'slug' => $page['Slug']
        ];
    }

    /**
     * Formats sections with grouped items.
     */
    private function formatSections(): array
    {
        $sections = [];

        foreach ($this->pageData['sections'] as $section) {
            $sections[] = [
                'id' => $section['sectionId'],
                'key' => $section['sectionKey'],
                'displayName' => $section['displayName'],
                'isEditable' => $this->isSectionEditable($section['sectionKey']),
                'items' => $this->groupItemsByType($section['items'])
            ];
        }

        return $sections;
    }

    /**
     * Groups items by their input type for easier rendering.
     */
    private function groupItemsByType(array $items): array
    {
        $grouped = [
            'text' => [],
            'tinymce' => [],
            'file' => []
        ];

        foreach ($items as $item) {
            $inputType = $item['inputType'];
            if (isset($grouped[$inputType])) {
                $grouped[$inputType][] = $item;
            } else {
                $grouped['text'][] = $item;
            }
        }

        // Flatten back to single array, sorted by type
        $result = [];
        foreach (['text', 'tinymce', 'file'] as $type) {
            foreach ($grouped[$type] as $item) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Checks if a section is editable.
     * Some sections like navbar/footer are not editable via page editor.
     */
    private function isSectionEditable(string $sectionKey): bool
    {
        $nonEditableSections = ['global_ui', 'schedule_section'];
        return !in_array($sectionKey, $nonEditableSections, true);
    }

    /**
     * Gets text content limits for JavaScript validation.
     */
    private function getContentLimits(): array
    {
        return [
            'HEADING' => CmsContentLimits::HEADING_MAX_CHARS,
            'TEXT' => CmsContentLimits::TEXT_MAX_CHARS,
            'HTML' => CmsContentLimits::HTML_MAX_CHARS,
            'BUTTON_TEXT' => CmsContentLimits::BUTTON_MAX_CHARS
        ];
    }

    /**
     * Gets image limits for JavaScript validation.
     */
    private function getImageLimits(): array
    {
        return [
            'maxWidth' => CmsContentLimits::IMAGE_MAX_WIDTH,
            'maxHeight' => CmsContentLimits::IMAGE_MAX_HEIGHT,
            'maxFileSize' => CmsContentLimits::IMAGE_MAX_FILE_SIZE,
            'maxFileSizeFormatted' => $this->formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE),
            'allowedMimes' => CmsContentLimits::IMAGE_ALLOWED_MIMES,
            'allowedExtensions' => ['jpg', 'jpeg', 'png', 'webp']
        ];
    }

    /**
     * Formats file size for display.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}


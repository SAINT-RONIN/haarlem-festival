<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\PageStatus;
use App\Models\CmsPage;
use App\Utils\CmsContentLimits;
use App\Utils\TimeFormatter;
use App\ViewModels\Cms\ActivityViewModel;
use App\ViewModels\Cms\DashboardViewModel;
use App\ViewModels\Cms\PageListItemViewModel;
use App\ViewModels\Cms\PagesListViewModel;
use App\ViewModels\Cms\RecentPageViewModel;

class CmsDashboardMapper
{
    /**
     * @param CmsPage[] $recentPages
     * @param array[]   $activities  Each entry: ['icon', 'text', 'time', 'color']
     */
    public static function toDashboardViewModel(array $recentPages, array $activities, string $userName): DashboardViewModel
    {
        return new DashboardViewModel(
            recentPages: array_map([self::class, 'toRecentPageViewModel'], $recentPages),
            activities: array_map([self::class, 'toActivityViewModel'], $activities),
            userName: $userName,
        );
    }

    /**
     * @param CmsPage[] $allPages
     */
    public static function toPagesListViewModel(array $allPages, string $searchQuery, string $userName): PagesListViewModel
    {
        return new PagesListViewModel(
            pages: array_map([self::class, 'toPageListItemViewModel'], $allPages),
            searchQuery: $searchQuery,
            userName: $userName,
        );
    }

    public static function toRecentPageViewModel(CmsPage $page): RecentPageViewModel
    {
        return new RecentPageViewModel(
            title: $page->title,
            status: self::resolvePageStatus($page)->value,
            timeAgo: TimeFormatter::formatTimeAgo($page->updatedAtUtc?->format('Y-m-d H:i:s')),
        );
    }

    public static function toPageListItemViewModel(CmsPage $page): PageListItemViewModel
    {
        return new PageListItemViewModel(
            id: $page->cmsPageId,
            title: $page->title,
            slug: $page->slug,
            status: self::resolvePageStatus($page)->value,
            updatedAt: TimeFormatter::formatTimeAgo($page->updatedAtUtc?->format('Y-m-d H:i:s')),
        );
    }

    private static function resolvePageStatus(CmsPage $page): PageStatus
    {
        return $page->updatedAtUtc !== null ? PageStatus::Published : PageStatus::Draft;
    }

    /**
     * @param array{icon: string, text: string, time: string, color: string} $activity
     */
    private static function toActivityViewModel(array $activity): ActivityViewModel
    {
        return new ActivityViewModel(
            icon: $activity['icon'],
            text: $activity['text'],
            time: $activity['time'],
            color: $activity['color'],
        );
    }

    public static function toPageEditViewData(array $pageData): array
    {
        return [
            'page' => self::formatPage($pageData),
            'sections' => self::formatSections($pageData),
            'contentLimits' => self::getContentLimits(),
            'imageLimits' => self::getImageLimits(),
        ];
    }

    private static function formatPage(array $pageData): array
    {
        $page = $pageData['page'];
        return [
            'id' => $page->cmsPageId,
            'title' => $page->title,
            'slug' => $page->slug,
        ];
    }

    private static function formatSections(array $pageData): array
    {
        $sections = [];
        foreach ($pageData['sections'] as $section) {
            $sections[] = [
                'id' => $section['sectionId'],
                'key' => $section['sectionKey'],
                'displayName' => $section['displayName'],
                'isEditable' => self::isSectionEditable($section['sectionKey']),
                'items' => self::groupItemsByType($section['items']),
                'subGroups' => self::buildSubGroups($section['sectionKey'], $section['items']),
            ];
        }
        return $sections;
    }

    private static function groupItemsByType(array $items): array
    {
        $grouped = ['text' => [], 'tinymce' => [], 'file' => []];
        foreach ($items as $item) {
            $inputType = $item['inputType'];
            if (isset($grouped[$inputType])) {
                $grouped[$inputType][] = $item;
            } else {
                $grouped['text'][] = $item;
            }
        }

        $result = [];
        foreach (['text', 'tinymce', 'file'] as $type) {
            foreach ($grouped[$type] as $item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Groups schedule_section items into logical sub-categories for a cleaner CMS editor.
     * Returns null for non-schedule sections (they use the flat items list).
     *
     * @return array<array{label: string, icon: string, color: string, columns: int, items: array}>|null
     */
    private static function buildSubGroups(string $sectionKey, array $items): ?array
    {
        if ($sectionKey !== 'schedule_section') {
            return null;
        }

        $groups = [
            'general' => ['label' => 'General Settings', 'icon' => 'settings', 'color' => 'blue', 'columns' => 2, 'items' => []],
            'display' => ['label' => 'Display Options', 'icon' => 'eye', 'color' => 'amber', 'columns' => 3, 'items' => []],
            'buttons' => ['label' => 'Button & Label Text', 'icon' => 'type', 'color' => 'emerald', 'columns' => 2, 'items' => []],
            'info' => ['label' => 'Additional Information', 'icon' => 'info', 'color' => 'purple', 'columns' => 1, 'items' => []],
            'filters' => ['label' => 'Filter Labels', 'icon' => 'filter', 'color' => 'rose', 'columns' => 3, 'items' => []],
        ];

        foreach ($items as $item) {
            $key = $item['itemKey'];
            $group = self::resolveItemGroup($key);
            $groups[$group]['items'][] = $item;
        }

        // Remove empty groups and re-index
        return array_values(array_filter($groups, fn(array $g) => $g['items'] !== []));
    }

    private static function resolveItemGroup(string $itemKey): string
    {
        if (str_starts_with($itemKey, 'schedule_filter_')) {
            return 'filters';
        }
        if (str_starts_with($itemKey, 'schedule_additional_info_')) {
            return 'info';
        }
        if (str_starts_with($itemKey, 'schedule_show_')) {
            return 'display';
        }
        if (in_array($itemKey, ['schedule_title', 'schedule_year'], true)) {
            return 'general';
        }
        return 'buttons';
    }

    private static function isSectionEditable(string $sectionKey): bool
    {
        $nonEditableSections = ['global_ui'];
        return !in_array($sectionKey, $nonEditableSections, true);
    }

    private static function getContentLimits(): array
    {
        return [
            'HEADING' => CmsContentLimits::HEADING_MAX_CHARS,
            'TEXT' => CmsContentLimits::TEXT_MAX_CHARS,
            'HTML' => CmsContentLimits::HTML_MAX_CHARS,
            'BUTTON_TEXT' => CmsContentLimits::BUTTON_MAX_CHARS,
        ];
    }

    private static function getImageLimits(): array
    {
        return [
            'maxWidth' => CmsContentLimits::IMAGE_MAX_WIDTH,
            'maxHeight' => CmsContentLimits::IMAGE_MAX_HEIGHT,
            'maxFileSize' => CmsContentLimits::IMAGE_MAX_FILE_SIZE,
            'maxFileSizeFormatted' => self::formatFileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE),
            'allowedMimes' => CmsContentLimits::IMAGE_ALLOWED_MIMES,
            'allowedExtensions' => ['jpg', 'jpeg', 'png', 'webp'],
        ];
    }

    private static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}

<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Enums\PageStatus;
use App\Helpers\FormatHelper;
use App\DTOs\Cms\ActivityData;
use App\DTOs\Cms\CmsItemEditData;
use App\DTOs\Cms\CmsMediaAssetData;
use App\Models\CmsPage;
use App\DTOs\Cms\CmsPageEditData;
use App\DTOs\Cms\CmsSectionEditData;
use App\Utils\CmsContentLimits;
use App\Utils\TimeFormatter;
use App\ViewModels\Cms\ActivityViewModel;
use App\ViewModels\Cms\CmsImageLimitsViewModel;
use App\ViewModels\Cms\CmsItemDisplayViewModel;
use App\ViewModels\Cms\CmsMediaAssetDisplayViewModel;
use App\ViewModels\Cms\CmsPageEditViewModel;
use App\ViewModels\Cms\CmsPageInfoViewModel;
use App\ViewModels\Cms\CmsSectionDisplayViewModel;
use App\ViewModels\Cms\DashboardViewModel;
use App\ViewModels\Cms\PageListItemViewModel;
use App\ViewModels\Cms\PagesListViewModel;
use App\ViewModels\Cms\RecentPageViewModel;

/**
 * Transforms CMS page and activity domain models into ViewModels for the CMS dashboard,
 * page list, and page-edit screens.
 *
 * Delegates section sub-group routing to CmsSectionGroupRouter.
 */
final class CmsDashboardViewMapper
{
    /**
     * Builds the CMS dashboard ViewModel showing recently-edited pages and activity feed.
     *
     * @param CmsPage[]      $recentPages
     * @param ActivityData[] $activities
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
     * Builds the CMS pages-list ViewModel used on the "All Pages" management screen.
     *
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

    /** Converts a CmsPage into a dashboard "recent page" card with a relative time-ago label. */
    public static function toRecentPageViewModel(CmsPage $page): RecentPageViewModel
    {
        return new RecentPageViewModel(
            title: $page->title,
            status: self::resolvePageStatus($page)->value,
            timeAgo: TimeFormatter::formatTimeAgo($page->updatedAtUtc?->format('Y-m-d H:i:s')),
        );
    }

    /** Converts a CmsPage into a list-row ViewModel for the pages-list table. */
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

    /** Derives Published/Draft status: a page with an updatedAt timestamp is considered published. */
    private static function resolvePageStatus(CmsPage $page): PageStatus
    {
        return $page->updatedAtUtc !== null ? PageStatus::Published : PageStatus::Draft;
    }

    /** Converts an ActivityData DTO into an ActivityViewModel for the dashboard feed. */
    private static function toActivityViewModel(ActivityData $activity): ActivityViewModel
    {
        return new ActivityViewModel(
            icon: $activity->icon,
            text: $activity->text,
            time: $activity->time,
            color: $activity->color,
        );
    }

    /**
     * Builds the full page-editor ViewModel: page info, grouped sections/items,
     * and content/image upload limits. Consumed by the CMS page-edit view.
     */
    public static function toPageEditViewData(CmsPageEditData $pageData): CmsPageEditViewModel
    {
        return new CmsPageEditViewModel(
            page: self::formatPage($pageData->page),
            sections: array_map([self::class, 'formatSingleSection'], $pageData->sections),
            contentLimits: self::getContentLimits(),
            imageLimits: self::getImageLimits(),
        );
    }

    /** Converts a CmsPage into a minimal page info ViewModel for the editor header. */
    private static function formatPage(CmsPage $page): CmsPageInfoViewModel
    {
        return new CmsPageInfoViewModel(
            id:    $page->cmsPageId,
            title: $page->title,
            slug:  $page->slug,
        );
    }

    /** Formats a single section into a display ViewModel with grouped items and sub-groups. */
    private static function formatSingleSection(CmsSectionEditData $section): CmsSectionDisplayViewModel
    {
        return new CmsSectionDisplayViewModel(
            id:          $section->sectionId,
            key:         $section->sectionKey,
            displayName: self::resolveSectionDisplayName($section->displayName),
            isEditable:  self::isSectionEditable($section->sectionKey),
            items:       self::groupItemsByType($section->items),
            subGroups:   CmsSectionGroupRouter::buildSubGroups($section->sectionKey, $section->items),
        );
    }

    /** Converts underscore-delimited display names to human-readable title case. */
    private static function resolveSectionDisplayName(string $displayName): string
    {
        return str_contains($displayName, '_') ? self::formatItemKeyName($displayName) : $displayName;
    }

    /**
     * Groups items by input type (text, tinymce, file) in a consistent order.
     *
     * @param CmsItemEditData[] $items
     * @return CmsItemDisplayViewModel[]
     */
    private static function groupItemsByType(array $items): array
    {
        $grouped = self::bucketItemsByInputType($items);
        return self::flattenOrderedBuckets($grouped);
    }

    /**
     * Sorts items into input-type buckets for consistent editor layout.
     *
     * @param CmsItemEditData[] $items
     * @return array<string, CmsItemDisplayViewModel[]>
     */
    private static function bucketItemsByInputType(array $items): array
    {
        $grouped = ['text' => [], 'tinymce' => [], 'file' => []];
        foreach ($items as $item) {
            $inputType = $item->inputType;
            $bucket = isset($grouped[$inputType]) ? $inputType : 'text';
            $grouped[$bucket][] = self::toItemViewModel($item);
        }
        return $grouped;
    }

    /**
     * Flattens input-type buckets into a single ordered list: text → tinymce → file.
     *
     * @param array<string, CmsItemDisplayViewModel[]> $grouped
     * @return CmsItemDisplayViewModel[]
     */
    private static function flattenOrderedBuckets(array $grouped): array
    {
        return array_merge($grouped['text'] ?? [], $grouped['tinymce'] ?? [], $grouped['file'] ?? []);
    }

    /**
     * Converts a CmsItemEditData into a display ViewModel for the page editor.
     * Public so CmsSectionGroupRouter can call it when distributing items into sub-groups.
     */
    public static function toItemViewModel(CmsItemEditData $item): CmsItemDisplayViewModel
    {
        return new CmsItemDisplayViewModel(
            itemId:       $item->itemId,
            itemKey:      $item->itemKey,
            displayName:  self::formatItemKeyName($item->displayName),
            type:         $item->type,
            typeLabel:    $item->typeLabel,
            inputType:    $item->inputType,
            maxChars:     $item->maxChars,
            value:        $item->value,
            mediaAssetId: $item->mediaAssetId,
            mediaAsset:   self::toMediaAssetViewModel($item->mediaAsset),
        );
    }

    /** Converts a media asset DTO into a display ViewModel, or null if no asset. */
    private static function toMediaAssetViewModel(?CmsMediaAssetData $asset): ?CmsMediaAssetDisplayViewModel
    {
        if ($asset === null) {
            return null;
        }

        return new CmsMediaAssetDisplayViewModel(
            filePath:         $asset->filePath,
            originalFileName: $asset->originalFileName,
            altText:          self::formatItemKeyName($asset->altText),
        );
    }

    /** Converts an underscore-delimited key into a human-readable title case label. */
    private static function formatItemKeyName(string $itemKey): string
    {
        return ucwords(str_replace('_', ' ', $itemKey));
    }

    /** Sections in the deny-list (e.g. global_ui) are rendered read-only in the editor. */
    private static function isSectionEditable(string $sectionKey): bool
    {
        $nonEditableSections = ['global_ui'];
        return !in_array($sectionKey, $nonEditableSections, true);
    }

    /**
     * Returns content character limits for the CMS editor validation.
     *
     * @return array{HEADING: int, TEXT: int, HTML: int, BUTTON_TEXT: int}
     */
    private static function getContentLimits(): array
    {
        return [
            'HEADING' => CmsContentLimits::HEADING_MAX_CHARS,
            'TEXT' => CmsContentLimits::TEXT_MAX_CHARS,
            'HTML' => CmsContentLimits::HTML_MAX_CHARS,
            'BUTTON_TEXT' => CmsContentLimits::BUTTON_MAX_CHARS,
        ];
    }

    /** Returns image upload limits for the CMS editor. */
    private static function getImageLimits(): CmsImageLimitsViewModel
    {
        return new CmsImageLimitsViewModel(
            maxWidth:             CmsContentLimits::IMAGE_MAX_WIDTH,
            maxHeight:            CmsContentLimits::IMAGE_MAX_HEIGHT,
            maxFileSize:          CmsContentLimits::IMAGE_MAX_FILE_SIZE,
            maxFileSizeFormatted: FormatHelper::fileSize(CmsContentLimits::IMAGE_MAX_FILE_SIZE),
            allowedMimes:         CmsContentLimits::IMAGE_ALLOWED_MIMES,
            allowedExtensions:    ['jpg', 'jpeg', 'png', 'webp'],
        );
    }
}

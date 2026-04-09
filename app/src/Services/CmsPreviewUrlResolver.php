<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\RestaurantDetailConstants;
use App\Constants\RouteConstants;
use App\Constants\StorytellingDetailConstants;
use App\DTOs\Cms\CmsSectionEditData;
use App\Models\CmsPage;
use App\Services\Interfaces\ICmsPreviewUrlResolver;

/**
 * Builds route-aware preview URLs for CMS page edit screens.
 *
 * For detail pages (storytelling-detail, restaurant-detail)
 * it extracts the first event name/ID from the sections to build a slug-based URL.
 * For all other pages it returns /{pageSlug} (or "/" for "home").
 */
final class CmsPreviewUrlResolver implements ICmsPreviewUrlResolver
{
    /**
     * @param CmsSectionEditData[] $sections
     */
    public function resolve(CmsPage $page, array $sections): string
    {
        if ($page->slug === GlobalUiConstants::PAGE_SLUG) {
            return '/';
        }

        $detailUrl = $this->resolveDetailPageUrl($page->slug, $sections);
        return $detailUrl ?? '/' . $page->slug;
    }

    /**
     * @param CmsSectionEditData[] $sections
     */
    private function resolveDetailPageUrl(string $slug, array $sections): ?string
    {
        if ($slug === StorytellingDetailConstants::DETAIL_PAGE_SLUG) {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null
                ? RouteConstants::STORYTELLING . '/' . $this->toSlug($eventName)
                : RouteConstants::STORYTELLING;
        }

        if ($slug === RestaurantDetailConstants::PAGE_SLUG) {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null
                ? RouteConstants::RESTAURANT . '/' . $this->toSlug($eventName)
                : RouteConstants::RESTAURANT;
        }

        return null;
    }

    /**
     * @param CmsSectionEditData[] $sections
     */
    private function extractFirstEventDisplayName(array $sections): ?string
    {
        foreach ($sections as $section) {
            if (!str_starts_with($section->sectionKey, 'event_')) {
                continue;
            }

            $displayName = trim($section->displayName);
            if ($displayName !== '') {
                return $displayName;
            }
        }

        return null;
    }

    private function toSlug(string $value): string
    {
        $lower = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $lower);
        return trim((string) $slug, '-');
    }
}

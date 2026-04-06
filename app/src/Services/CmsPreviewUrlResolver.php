<?php

declare(strict_types=1);

namespace App\Services;

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
        if ($page->slug === 'home') {
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
        if ($slug === 'storytelling-detail') {
            $eventName = $this->extractFirstEventDisplayName($sections);
            return $eventName !== null ? '/storytelling/' . $this->toSlug($eventName) : '/storytelling';
        }

        if ($slug === 'restaurant-detail') {
            $eventId = $this->extractFirstEventId($sections);
            return $eventId !== null ? '/restaurant/' . $eventId : '/restaurant';
        }

        return null;
    }

    /**
     * @param CmsSectionEditData[] $sections
     */
    private function extractFirstEventId(array $sections): ?int
    {
        foreach ($sections as $section) {
            if (preg_match('/^event_(\d+)$/', $section->sectionKey, $matches) === 1) {
                return (int)$matches[1];
            }
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
        return trim((string)$slug, '-');
    }
}

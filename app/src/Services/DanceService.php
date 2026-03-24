<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CmsItemFilter;
use App\Models\CmsPageFilter;
use App\Repositories\CmsRepository;
use App\Services\Interfaces\IDanceService;

final class DanceService implements IDanceService
{
    public function __construct(
        private readonly CmsRepository $cmsRepository = new CmsRepository(),
        private readonly DanceArtistContentProvider $artistContentProvider = new DanceArtistContentProvider(),
    ) {
    }

    public function getPageData(): array
    {
        $fallback = [
            'hero' => [
                'title' => 'HAARLEM DANCE',
                'subtitle' => "Experience high-energy dance performances at Haarlem's premier music festival. Discover our complete lineup, detailed schedules, and venue information.",
                'primaryButtonText' => 'Discover all performances',
                'primaryButtonLink' => '/dance#artists',
                'secondaryButtonText' => 'What is Haarlem Dance?',
                'secondaryButtonLink' => '/dance#about',
                'backgroundImage' => '/assets/Image/Image (Dance).png',
            ],
            'gradient' => [
                'heading' => 'Every beat carries energy, movement, and connection beyond what is heard.',
                'subheading' => 'A place where dance is experienced, not just played.',
                'backgroundImage' => '/assets/Image/dance/banner.jpg',
            ],
            'intro' => [
                'heading' => 'Move to the rhythm of Haarlem Dance',
                'body' => "Haarlem Dance brings together electronic music, unforgettable artists, and vibrant performances across the city.\n\nExplore featured artists, detailed schedules, venues, and ticket options.",
                'image' => '/assets/Image/dance/dance.jpg',
                'imageAlt' => 'Dance festival performance',
            ],
        ];

        $pages = $this->cmsRepository->findPages(new CmsPageFilter(slug: 'dance'));
        $page = $pages[0] ?? null;

        if ($page !== null) {
            $pageId = $page->cmsPageId;

            $heroItems = $this->mapItemsByKey($this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId, sectionKey: 'hero_section')));
            $gradientItems = $this->mapItemsByKey($this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId, sectionKey: 'gradient_section')));
            $introItems = $this->mapItemsByKey($this->cmsRepository->findItems(new CmsItemFilter(cmsPageId: $pageId, sectionKey: 'intro_split_section')));

            $fallback['hero'] = [
                'title' => $heroItems['hero_main_title'] ?? $fallback['hero']['title'],
                'subtitle' => $heroItems['hero_subtitle'] ?? $fallback['hero']['subtitle'],
                'primaryButtonText' => $heroItems['hero_button_primary'] ?? $fallback['hero']['primaryButtonText'],
                'primaryButtonLink' => $heroItems['hero_button_primary_link'] ?? $fallback['hero']['primaryButtonLink'],
                'secondaryButtonText' => $heroItems['hero_button_secondary'] ?? $fallback['hero']['secondaryButtonText'],
                'secondaryButtonLink' => $heroItems['hero_button_secondary_link'] ?? $fallback['hero']['secondaryButtonLink'],
                'backgroundImage' => $heroItems['hero_background_image'] ?? $fallback['hero']['backgroundImage'],
            ];

            $fallback['gradient'] = [
                'heading' => $gradientItems['gradient_heading'] ?? $fallback['gradient']['heading'],
                'subheading' => $gradientItems['gradient_subheading'] ?? $fallback['gradient']['subheading'],
                'backgroundImage' => $gradientItems['gradient_background_image'] ?? $fallback['gradient']['backgroundImage'],
            ];

            $fallback['intro'] = [
                'heading' => $introItems['intro_heading'] ?? $fallback['intro']['heading'],
                'body' => $introItems['intro_body'] ?? $fallback['intro']['body'],
                'image' => $introItems['intro_image'] ?? $fallback['intro']['image'],
                'imageAlt' => $introItems['intro_image_alt'] ?? $fallback['intro']['imageAlt'],
            ];
        }

        return [
            'hero' => $fallback['hero'],
            'gradient' => $fallback['gradient'],
            'intro' => $fallback['intro'],
            'artists' => $this->artistContentProvider->getAll(),
        ];
    }

    public function getArtistDetailBySlug(string $slug): ?array
    {
        return $this->artistContentProvider->getBySlug($slug);
    }

    /**
     * @param array<int, object> $items
     * @return array<string, string>
     */
    private function mapItemsByKey(array $items): array
    {
        $mapped = [];

        foreach ($items as $item) {
            $itemKey = $item->itemKey ?? null;

            if (!is_string($itemKey) || $itemKey === '') {
                continue;
            }

            $value = $item->textValue ?? $item->htmlValue ?? null;

            if ($value !== null) {
                $mapped[$itemKey] = (string) $value;
            }
        }

        return $mapped;
    }
}
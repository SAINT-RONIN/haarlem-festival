<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\CmsItemEditData;
use App\ViewModels\Cms\CmsSubGroupViewModel;

/**
 * Routes CMS section items into logical sub-groups for the page editor.
 *
 * Each section type has its own group definitions (e.g. "Hero Content",
 * "Call-to-Action Buttons") and routing rules that decide which group
 * an item belongs to based on its item key.
 *
 * Called by CmsDashboardViewMapper when building section ViewModels.
 */
final class CmsSectionGroupRouter
{
    /**
     * Groups section items into logical sub-categories for a cleaner CMS editor.
     * Returns null for small sections that don't need grouping.
     *
     * @param CmsItemEditData[] $items
     * @return CmsSubGroupViewModel[]|null
     */
    public static function buildSubGroups(string $sectionKey, array $items): ?array
    {
        if (count($items) < 6) {
            return null;
        }

        $groupDefs = self::getGroupDefinitions($sectionKey);
        if ($groupDefs === null) {
            return null;
        }

        return self::distributeItemsIntoGroups($sectionKey, $items, $groupDefs);
    }

    /**
     * @param CmsItemEditData[] $items
     * @param array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}> $groupDefs
     * @return CmsSubGroupViewModel[]
     */
    private static function distributeItemsIntoGroups(string $sectionKey, array $items, array $groupDefs): array
    {
        foreach ($items as $item) {
            $target = self::routeItemToGroup($sectionKey, $item->itemKey, $groupDefs);
            if (!isset($groupDefs[$target])) {
                continue;
            }
            $groupDefs[$target]['items'][] = CmsDashboardViewMapper::toItemViewModel($item);
        }
        return self::buildSubGroupViewModels($groupDefs);
    }

    /**
     * @param array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}> $groupDefs
     * @return CmsSubGroupViewModel[]
     */
    private static function buildSubGroupViewModels(array $groupDefs): array
    {
        $filledGroups = array_filter($groupDefs, fn(array $g) => $g['items'] !== []);
        return array_values(array_map(
            fn(array $g) => new CmsSubGroupViewModel(
                label:   $g['label'],
                icon:    $g['icon'],
                color:   $g['color'],
                columns: $g['columns'],
                items:   $g['items'],
            ),
            $filledGroups,
        ));
    }

    // ── Group Definition Routing ──

    /**
     * Returns the group definitions for a section, or null if no grouping is defined.
     *
     * @return array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}>|null
     */
    private static function getGroupDefinitions(string $sectionKey): ?array
    {
        return self::getNamedSectionGroups($sectionKey) ?? self::getFallbackSectionGroups($sectionKey);
    }

    /**
     * @return array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}>|null
     */
    private static function getNamedSectionGroups(string $sectionKey): ?array
    {
        return self::getJazzPageSectionGroups($sectionKey) ?? self::getOtherPageSectionGroups($sectionKey);
    }

    /**
     * @return array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}>|null
     */
    private static function getJazzPageSectionGroups(string $sectionKey): ?array
    {
        return match ($sectionKey) {
            'schedule_section'             => self::scheduleGroups(),
            'hero_section'                 => self::heroGroups(),
            'masonry_section'              => self::masonryGroups(),
            'venues_section'               => self::venuesGroups(),
            'pricing_section'              => self::pricingGroups(),
            'artists_section'              => self::artistsGroups(),
            'historical_locations_section' => self::historicalLocationsGroups(),
            default => null,
        };
    }

    /**
     * @return array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}>|null
     */
    private static function getOtherPageSectionGroups(string $sectionKey): ?array
    {
        return match ($sectionKey) {
            'route_section'                       => self::routeSectionGroups(),
            'history_pricing_section'             => self::historyPricingGroups(),
            'history_important_tour_info_section' => self::historyInfoGroups(),
            'detail_section'                      => self::restaurantDetailGroups(),
            'venue_map_section'                   => self::venueMapGroups(),
            'global_ui'                           => self::globalUiGroups(),
            'main'                                => self::mainSectionGroups(),
            default => null,
        };
    }

    /**
     * @return array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}>|null
     */
    private static function getFallbackSectionGroups(string $sectionKey): ?array
    {
        return str_starts_with($sectionKey, 'event_') ? self::eventDetailGroups() : null;
    }

    // ── Item Routing ──

    /**
     * Routes an item to its group based on section and item key.
     *
     * @param array<string, array{label: string, icon: string, color: string, columns: int, items: \App\ViewModels\Cms\CmsItemDisplayViewModel[]}> $groupDefs
     */
    private static function routeItemToGroup(string $sectionKey, string $itemKey, array $groupDefs): string
    {
        return self::routeJazzPageItem($sectionKey, $itemKey)
            ?? self::routeOtherPageItem($sectionKey, $itemKey)
            ?? 'other';
    }

    private static function routeJazzPageItem(string $sectionKey, string $itemKey): ?string
    {
        return match ($sectionKey) {
            'schedule_section'             => self::resolveScheduleGroup($itemKey),
            'hero_section'                 => self::resolveHeroGroup($itemKey),
            'masonry_section'              => str_starts_with($itemKey, 'masonry_image') ? 'images' : 'content',
            'venues_section'               => self::resolveVenuesGroup($itemKey),
            'pricing_section'              => self::resolvePricingGroup($itemKey),
            'artists_section'              => self::resolveArtistsGroup($itemKey),
            'historical_locations_section' => self::resolveHistoricalLocationsGroup($itemKey),
            default => null,
        };
    }

    private static function routeOtherPageItem(string $sectionKey, string $itemKey): ?string
    {
        return match ($sectionKey) {
            'route_section'                       => str_starts_with($itemKey, 'route_location') ? 'locations' : 'header',
            'history_pricing_section'             => str_contains($itemKey, '_group_') ? 'group' : 'single',
            'history_important_tour_info_section' => str_starts_with($itemKey, 'important_info_') ? 'items' : 'header',
            'detail_section'                      => self::resolveRestaurantDetailGroup($itemKey),
            'venue_map_section'                   => str_starts_with($itemKey, 'venue_map_filter_') ? 'filters' : 'content',
            'global_ui'                           => self::resolveGlobalUiGroup($itemKey),
            'main'                                => self::resolveMainGroup($itemKey),
            default => str_starts_with($sectionKey, 'event_') ? self::resolveEventDetailGroup($itemKey) : null,
        };
    }

    // ── Schedule Section ──

    private static function scheduleGroups(): array
    {
        return [
            'general' => ['label' => 'General Settings',      'icon' => 'settings', 'color' => 'blue',    'columns' => 2, 'items' => []],
            'display' => ['label' => 'Display Options',        'icon' => 'eye',      'color' => 'amber',   'columns' => 3, 'items' => []],
            'buttons' => ['label' => 'Button & Label Text',    'icon' => 'type',     'color' => 'emerald', 'columns' => 2, 'items' => []],
            'info'    => ['label' => 'Additional Information', 'icon' => 'info',     'color' => 'purple',  'columns' => 1, 'items' => []],
            'filters' => ['label' => 'Filter Labels',          'icon' => 'filter',   'color' => 'rose',    'columns' => 3, 'items' => []],
        ];
    }

    private static function resolveScheduleGroup(string $itemKey): string
    {
        if (str_starts_with($itemKey, 'schedule_filter_')) return 'filters';
        if (str_starts_with($itemKey, 'schedule_additional_info_')) return 'info';
        if (str_starts_with($itemKey, 'schedule_show_')) return 'display';
        if (in_array($itemKey, ['schedule_title', 'schedule_year'], true)) return 'general';
        return 'buttons';
    }

    // ── Hero Section ──

    private static function heroGroups(): array
    {
        return [
            'content' => ['label' => 'Text Content',           'icon' => 'type',               'color' => 'blue',    'columns' => 2, 'items' => []],
            'buttons' => ['label' => 'Call-to-Action Buttons', 'icon' => 'mouse-pointer-click', 'color' => 'emerald', 'columns' => 2, 'items' => []],
            'media'   => ['label' => 'Background Image',       'icon' => 'image',              'color' => 'amber',   'columns' => 1, 'items' => []],
        ];
    }

    private static function resolveHeroGroup(string $itemKey): string
    {
        if (str_contains($itemKey, 'button') || str_contains($itemKey, 'link')) return 'buttons';
        if (str_contains($itemKey, 'image') || str_contains($itemKey, 'background')) return 'media';
        return 'content';
    }

    // ── Masonry Section ──

    private static function masonryGroups(): array
    {
        return [
            'content' => ['label' => 'Section Heading', 'icon' => 'type',  'color' => 'blue',  'columns' => 1, 'items' => []],
            'images'  => ['label' => 'Gallery Images',  'icon' => 'image', 'color' => 'amber', 'columns' => 3, 'items' => []],
        ];
    }

    // ── Jazz Venues Section ──

    private static function venuesGroups(): array
    {
        return [
            'header'      => ['label' => 'Section Header', 'icon' => 'type',     'color' => 'blue',  'columns' => 1, 'items' => []],
            'patronaat'   => ['label' => 'Patronaat',       'icon' => 'building', 'color' => 'rose',  'columns' => 2, 'items' => []],
            'grotemarkt'  => ['label' => 'Grote Markt',     'icon' => 'building', 'color' => 'amber', 'columns' => 2, 'items' => []],
        ];
    }

    private static function resolveVenuesGroup(string $itemKey): string
    {
        if (str_contains($itemKey, 'patronaat')) return 'patronaat';
        if (str_contains($itemKey, 'grotemarkt')) return 'grotemarkt';
        return 'header';
    }

    // ── Jazz Pricing Section ──

    private static function pricingGroups(): array
    {
        return [
            'header'     => ['label' => 'Section Header',    'icon' => 'type',           'color' => 'blue',    'columns' => 1, 'items' => []],
            'individual' => ['label' => 'Individual Tickets', 'icon' => 'ticket',         'color' => 'emerald', 'columns' => 2, 'items' => []],
            'daypass'    => ['label' => 'Day Pass',           'icon' => 'calendar',       'color' => 'amber',   'columns' => 2, 'items' => []],
            'threeday'   => ['label' => '3-Day Pass',         'icon' => 'calendar-range', 'color' => 'purple',  'columns' => 2, 'items' => []],
        ];
    }

    private static function resolvePricingGroup(string $itemKey): string
    {
        if (str_contains($itemKey, '_individual')) return 'individual';
        if (str_contains($itemKey, '_daypass')) return 'daypass';
        if (str_contains($itemKey, '_3day')) return 'threeday';
        return 'header';
    }

    // ── Jazz Artists Section ──

    private static function artistsGroups(): array
    {
        return [
            'header' => ['label' => 'Section Header', 'icon' => 'type', 'color' => 'blue', 'columns' => 1, 'items' => []],
        ];
    }

    private static function resolveArtistsGroup(string $itemKey): string
    {
        return 'header';
    }

    // ── History: Historical Locations Section ──

    private static function historicalLocationsGroups(): array
    {
        return [
            'header'           => ['label' => 'Section Header',    'icon' => 'type',    'color' => 'blue',    'columns' => 1, 'items' => []],
            'grotemarkt'       => ['label' => 'Grote Markt',        'icon' => 'map-pin', 'color' => 'rose',    'columns' => 1, 'items' => []],
            'amsterdamsepoort' => ['label' => 'Amsterdamse Poort',  'icon' => 'map-pin', 'color' => 'amber',   'columns' => 1, 'items' => []],
            'molendeadriaan'   => ['label' => 'Molen de Adriaan',   'icon' => 'map-pin', 'color' => 'emerald', 'columns' => 1, 'items' => []],
        ];
    }

    private static function resolveHistoricalLocationsGroup(string $itemKey): string
    {
        if (str_contains($itemKey, 'grotemarkt')) return 'grotemarkt';
        if (str_contains($itemKey, 'amsterdamsepoort')) return 'amsterdamsepoort';
        if (str_contains($itemKey, 'molendeadriaan')) return 'molendeadriaan';
        return 'header';
    }

    // ── History: Route Section ──

    private static function routeSectionGroups(): array
    {
        return [
            'header'    => ['label' => 'Section Header',   'icon' => 'type',    'color' => 'blue', 'columns' => 1, 'items' => []],
            'locations' => ['label' => 'Route Locations',  'icon' => 'map-pin', 'color' => 'rose', 'columns' => 2, 'items' => []],
        ];
    }

    // ── History: Pricing Section ──

    private static function historyPricingGroups(): array
    {
        return [
            'single' => ['label' => 'Single Ticket', 'icon' => 'ticket', 'color' => 'emerald', 'columns' => 2, 'items' => []],
            'group'  => ['label' => 'Group Ticket',  'icon' => 'users',  'color' => 'purple',  'columns' => 2, 'items' => []],
        ];
    }

    // ── History: Important Tour Info Section ──

    private static function historyInfoGroups(): array
    {
        return [
            'header' => ['label' => 'Section Header',    'icon' => 'type', 'color' => 'blue',   'columns' => 1, 'items' => []],
            'items'  => ['label' => 'Information Items', 'icon' => 'list', 'color' => 'purple', 'columns' => 2, 'items' => []],
        ];
    }

    // ── Restaurant: Detail Section ──

    private static function restaurantDetailGroups(): array
    {
        return [
            'hero'        => ['label' => 'Hero Content',            'icon' => 'image',    'color' => 'blue',    'columns' => 1, 'items' => []],
            'contact'     => ['label' => 'Contact Labels',          'icon' => 'phone',    'color' => 'rose',    'columns' => 2, 'items' => []],
            'practical'   => ['label' => 'Practical Info Labels',   'icon' => 'info',     'color' => 'amber',   'columns' => 2, 'items' => []],
            'sections'    => ['label' => 'Content Section Titles',  'icon' => 'type',     'color' => 'emerald', 'columns' => 2, 'items' => []],
            'reservation' => ['label' => 'Reservation Form',        'icon' => 'calendar', 'color' => 'purple',  'columns' => 1, 'items' => []],
        ];
    }

    private static function resolveRestaurantDetailGroup(string $itemKey): string
    {
        if (str_starts_with($itemKey, 'detail_hero_')) return 'hero';
        if (str_starts_with($itemKey, 'detail_contact_') || str_contains($itemKey, '_label_address') || str_contains($itemKey, '_label_contact') || str_contains($itemKey, '_label_open_hours')) return 'contact';
        if (str_starts_with($itemKey, 'detail_reservation_')) return 'reservation';
        if (str_contains($itemKey, '_label_')) return 'practical';
        return 'sections';
    }

    // ── Home: Venue Map Section ──

    private static function venueMapGroups(): array
    {
        return [
            'content' => ['label' => 'Section Content',   'icon' => 'type',   'color' => 'blue', 'columns' => 1, 'items' => []],
            'filters' => ['label' => 'Map Filter Labels', 'icon' => 'filter', 'color' => 'rose', 'columns' => 3, 'items' => []],
        ];
    }

    // ── Home: Global UI ──

    private static function globalUiGroups(): array
    {
        return [
            'site'       => ['label' => 'Site Identity',           'icon' => 'globe', 'color' => 'blue',    'columns' => 1, 'items' => []],
            'navigation' => ['label' => 'Navigation Labels',       'icon' => 'menu',  'color' => 'emerald', 'columns' => 3, 'items' => []],
            'buttons'    => ['label' => 'Global Buttons & Labels', 'icon' => 'type',  'color' => 'amber',   'columns' => 2, 'items' => []],
        ];
    }

    private static function resolveGlobalUiGroup(string $itemKey): string
    {
        if (str_starts_with($itemKey, 'nav_')) return 'navigation';
        if ($itemKey === 'site_name') return 'site';
        return 'buttons';
    }

    // ── Main Section (My Program, Checkout) ──

    private static function mainSectionGroups(): array
    {
        return [
            'content' => ['label' => 'Page Content',      'icon' => 'type',               'color' => 'blue',    'columns' => 2, 'items' => []],
            'buttons' => ['label' => 'Buttons & Labels',  'icon' => 'mouse-pointer-click', 'color' => 'emerald', 'columns' => 2, 'items' => []],
        ];
    }

    private static function resolveMainGroup(string $itemKey): string
    {
        if (str_contains($itemKey, 'btn') || str_contains($itemKey, 'button') || str_contains($itemKey, 'label')) return 'buttons';
        return 'content';
    }

    // ── Event Detail Pages (Storytelling Detail, Jazz Artist Detail) ──

    private static function eventDetailGroups(): array
    {
        return [
            'hero'       => ['label' => 'Hero & Navigation',  'icon' => 'image',       'color' => 'blue',    'columns' => 2, 'items' => []],
            'about'      => ['label' => 'About Section',      'icon' => 'type',        'color' => 'emerald', 'columns' => 1, 'items' => []],
            'highlights' => ['label' => 'Highlight Cards',    'icon' => 'star',        'color' => 'amber',   'columns' => 3, 'items' => []],
            'gallery'    => ['label' => 'Gallery Images',     'icon' => 'image',       'color' => 'rose',    'columns' => 3, 'items' => []],
            'media'      => ['label' => 'Video & Media',      'icon' => 'play-circle', 'color' => 'purple',  'columns' => 1, 'items' => []],
            'schedule'   => ['label' => 'Schedule Settings',  'icon' => 'calendar',    'color' => 'blue',    'columns' => 1, 'items' => []],
            'extra'      => ['label' => 'Additional Content', 'icon' => 'layout',      'color' => 'emerald', 'columns' => 2, 'items' => []],
        ];
    }

    private static function resolveEventDetailGroup(string $itemKey): string
    {
        if (str_contains($itemKey, 'hero') || str_contains($itemKey, 'back_button') || str_contains($itemKey, 'reserve_button')) return 'hero';
        if (str_starts_with($itemKey, 'about_')) return 'about';
        if (str_starts_with($itemKey, 'highlight_')) return 'highlights';
        if (str_starts_with($itemKey, 'gallery_')) return 'gallery';
        if (str_starts_with($itemKey, 'video_')) return 'media';
        if (str_contains($itemKey, 'schedule') || str_contains($itemKey, 'cta')) return 'schedule';
        return 'extra';
    }
}

<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class GlobalUiData
{
    private const DEFAULT_SITE_NAME = 'Haarlem Festivals';
    private const DEFAULT_NAV_HOME = 'Home';
    private const DEFAULT_NAV_JAZZ = 'Jazz';
    private const DEFAULT_NAV_DANCE = 'Dance';
    private const DEFAULT_NAV_HISTORY = 'History';
    private const DEFAULT_NAV_RESTAURANT = 'Restaurant';
    private const DEFAULT_NAV_STORYTELLING = 'Storytelling';
    private const DEFAULT_BTN_MY_PROGRAM = 'My Program';
    private const DEFAULT_LOGIN_LABEL = 'Login';
    private const DEFAULT_LOGOUT_LABEL = 'Logout';
    private const DEFAULT_LABEL_EVENTS_COUNT = 'events';
    private const DEFAULT_LABEL_NO_EVENTS = 'No events scheduled';
    private const DEFAULT_BTN_EXPLORE_TEMPLATE = 'Explore {title} Events';

    public function __construct(
        public string $siteName,
        public string $navHome,
        public string $navJazz,
        public string $navDance,
        public string $navHistory,
        public string $navRestaurant,
        public string $navStorytelling,
        public string $btnMyProgram,
        public string $loginLabel,
        public string $logoutLabel,
        public string $labelEventsCount,
        public string $labelNoEvents,
        public string $btnExploreTemplate,
        public bool   $isLoggedIn = false,
    ) {
    }

    public static function fromCms(array $content, bool $isLoggedIn): self
    {
        return new self(
            siteName: $content['site_name'] ?? self::DEFAULT_SITE_NAME,
            navHome: $content['nav_home'] ?? self::DEFAULT_NAV_HOME,
            navJazz: $content['nav_jazz'] ?? self::DEFAULT_NAV_JAZZ,
            navDance: $content['nav_dance'] ?? self::DEFAULT_NAV_DANCE,
            navHistory: $content['nav_history'] ?? self::DEFAULT_NAV_HISTORY,
            navRestaurant: $content['nav_restaurant'] ?? self::DEFAULT_NAV_RESTAURANT,
            navStorytelling: $content['nav_storytelling'] ?? self::DEFAULT_NAV_STORYTELLING,
            btnMyProgram: $content['btn_my_program'] ?? self::DEFAULT_BTN_MY_PROGRAM,
            loginLabel: $content['login_label'] ?? self::DEFAULT_LOGIN_LABEL,
            logoutLabel: $content['logout_label'] ?? self::DEFAULT_LOGOUT_LABEL,
            labelEventsCount: $content['label_events_count'] ?? self::DEFAULT_LABEL_EVENTS_COUNT,
            labelNoEvents: $content['label_no_events'] ?? self::DEFAULT_LABEL_NO_EVENTS,
            btnExploreTemplate: $content['btn_explore_template'] ?? self::DEFAULT_BTN_EXPLORE_TEMPLATE,
            isLoggedIn: $isLoggedIn,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Constants;

final class ScheduleConstants
{
    /** Maximum number of schedule days shown on listing and detail pages. */
    public const MAX_DAYS = 7;

    /** CMS section key for the schedule component embedded on event-type pages. */
    public const SCHEDULE_SECTION_KEY = 'schedule_section';

    /** Session query ordering used across all schedule queries. */
    public const ORDER_BY_START_DATETIME = 'es.StartDateTime ASC';

    // CMS display fallbacks — used when content editors leave a field empty
    public const DEFAULT_CTA_BUTTON_TEXT         = 'Discover';
    public const DEFAULT_PAY_WHAT_YOU_LIKE_TEXT  = 'Pay as you like';
    public const DEFAULT_CURRENCY_SYMBOL         = '€';
    public const DEFAULT_HISTORY_START_POINT     = 'A giant flag near Church of St. Bavo at Grote Markt';
    public const DEFAULT_HISTORY_GROUP_TICKET    = 'Group ticket- best value for 4 people';

    // Time-of-day buckets used for filtering and card display
    public const TIME_RANGE_MORNING   = 'morning';
    public const TIME_RANGE_AFTERNOON = 'afternoon';
    public const TIME_RANGE_EVENING   = 'evening';
    public const MORNING_HOUR_END     = 12;
    public const AFTERNOON_HOUR_END   = 17;

    // Price type identifiers used in filter options and event card rendering
    public const PRICE_TYPE_PAY_WHAT_YOU_LIKE = 'pay-what-you-like';
    public const PRICE_TYPE_FIXED             = 'fixed';
    public const PRICE_TYPE_FREE              = 'free';

    // CMS fallbacks for schedule mapper button and UI text
    public const DEFAULT_CONFIRM_TEXT         = 'Confirm selection';
    public const DEFAULT_ADDING_TEXT          = 'Adding...';
    public const DEFAULT_SUCCESS_TEXT         = 'Added to program';
    public const DEFAULT_NO_EVENTS_TEXT       = 'No events scheduled';
    public const DEFAULT_FILTERS_BUTTON_TEXT  = 'Filters';
    public const DEFAULT_ADDITIONAL_INFO_TITLE = 'Additional Information:';
    public const DEFAULT_RESET_FILTERS_TEXT   = 'Reset all filters';
    public const DEFAULT_SCHEDULE_YEAR        = '2026';
    public const DEFAULT_EVENT_COUNT_LABEL    = 'Events';

    // Filter type identifiers used to configure the schedule filter UI per event type
    public const FILTER_DAY        = 'day';
    public const FILTER_TIME_RANGE = 'timeRange';
    public const FILTER_PRICE_TYPE = 'priceType';
    public const FILTER_LANGUAGE   = 'language';
    public const FILTER_AGE_GROUP  = 'ageGroup';
    public const FILTER_VENUE      = 'venue';
    public const FILTER_START_TIME = 'startTime';

    private function __construct() {}
}

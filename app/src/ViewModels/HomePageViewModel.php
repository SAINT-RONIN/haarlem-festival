<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the homepage.
 *
 * Contains all pre-formatted data needed by the home page view.
 * The service prepares this data so the view only needs to loop and print.
 */
class HomePageViewModel
{
    /**
     * @param array $eventTypes Array of event type showcase data with precomputed styles
     * @param array $locations Array of venues/restaurants with category badge classes
     * @param array $scheduleDays Array of days, each with date info and sessions
     */
    public function __construct(
        public array $eventTypes = [],
        public array $locations = [],
        public array $scheduleDays = [],
    ) {
    }
}


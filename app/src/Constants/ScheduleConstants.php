<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Shared schedule configuration values used across Jazz, Storytelling, and other event types.
 */
final class ScheduleConstants
{
    /** Maximum number of schedule days shown on listing and detail pages. */
    public const MAX_DAYS = 7;

    private function __construct() {}
}

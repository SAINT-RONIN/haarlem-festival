<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use App\Services\Interfaces\IStorytellingService;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;
use App\ViewModels\Storytelling\MasonryImageData;
use App\ViewModels\Storytelling\MasonrySectionData;
use App\ViewModels\Storytelling\StorytellingPageViewModel;

/**
 * Service for preparing storytelling page data.
 *
 * Assembles all data needed for the storytelling page view.
 */
class StorytellingService implements IStorytellingService
{
    private CmsService $cmsService;
    private CmsEventsService $cmsEventsService;
    private EventSessionRepository $eventSessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private EventSessionPriceRepository $priceRepository;

    private const DEFAULT_IMAGE = '/assets/Image/Image (Story).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    // Masonry layout constants
    private const MASONRY_COLUMNS = 4;
    private const MASONRY_IMAGES_PER_COLUMN = 3;
    private const MASONRY_TOTAL_IMAGES = 12;

    // Price tier constants
    private const PRICE_TIER_ADULT = 1;
    private const PRICE_TIER_PAY_WHAT_YOU_LIKE = 5;

    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->cmsEventsService = new CmsEventsService();
        $this->eventSessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->priceRepository = new EventSessionPriceRepository();
    }

    /**
     * Builds the storytelling page view model with all required data.
     */
    public function getStorytellingPageData(): StorytellingPageViewModel
    {
        return new StorytellingPageViewModel(
            heroData: $this->cmsService->buildHeroData('storytelling', 'storytelling'),
            globalUi: $this->cmsService->buildGlobalUiData(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            masonrySection: $this->buildMasonrySection(),
            scheduleSection: $this->buildScheduleSection(),
        );
    }

    /**
     * Builds the gradient section data with background image from CMS.
     */
    private function buildGradientSection(): GradientSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'gradient_section');

        return new GradientSectionData(
            headingText: $this->getStringValue($content, 'gradient_heading', ''),
            subheadingText: $this->getStringValue($content, 'gradient_subheading', ''),
            backgroundImageUrl: $this->validateImagePath($content['gradient_background_image'] ?? ''),
        );
    }

    /**
     * Builds the intro split section data with image from CMS.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'intro_split_section');
        $heading = $this->getStringValue($content, 'intro_heading', 'Stories in Haarlem');

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $this->getStringValue($content, 'intro_body', ''),
            imageUrl: $this->validateImagePath($content['intro_image'] ?? ''),
            imageAltText: $heading, // Use heading as alt text
        );
    }

    /**
     * Builds the masonry section data with exactly 4 columns × 3 images.
     * Uses deterministic shuffle for stable ordering.
     */
    private function buildMasonrySection(): MasonrySectionData
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'masonry_section');

        return new MasonrySectionData(
            headingText: $this->getStringValue($content, 'masonry_heading', ''),
            columns: $this->buildMasonryColumns(),
        );
    }

    /**
     * Builds exactly 4 columns with 3 images each.
     * Applies deterministic shuffle to the image list for visual variety.
     */
    private function buildMasonryColumns(): array
    {
        $images = $this->buildShuffledMasonryImages();

        // Distribute into 4 columns × 3 images
        $columns = [];
        $index = 0;

        for ($col = 0; $col < self::MASONRY_COLUMNS; $col++) {
            $columns[$col] = [];
            for ($row = 0; $row < self::MASONRY_IMAGES_PER_COLUMN; $row++) {
                $columns[$col][] = $images[$index] ?? $this->createPlaceholderImage();
                $index++;
            }
        }

        return $columns;
    }

    /**
     * Builds masonry images from CMS content.
     * Reads image paths from CMS masonry_image_01 through masonry_image_12.
     * Assigns varying size classes for true masonry effect.
     */
    private function buildShuffledMasonryImages(): array
    {
        $content = $this->cmsService->getSectionContent('storytelling', 'masonry_section');

        // Collect image paths from CMS (masonry_image_01 through masonry_image_12)
        $imagePaths = [];
        for ($i = 1; $i <= self::MASONRY_TOTAL_IMAGES; $i++) {
            $key = sprintf('masonry_image_%02d', $i);
            $path = $content[$key] ?? null;
            if (!empty($path)) {
                $imagePaths[] = $path;
            }
        }

        // If no images found in CMS, return empty array
        if (empty($imagePaths)) {
            return [];
        }

        // Size class pattern for varied heights (repeating pattern)
        $sizeClasses = [
            'masonry-tall',
            'masonry-short',
            'masonry-medium',
            'masonry-medium',
            'masonry-tall',
            'masonry-short',
            'masonry-short',
            'masonry-medium',
            'masonry-tall',
            'masonry-medium',
            'masonry-short',
            'masonry-tall',
        ];

        // Build image DTOs with size classes
        $images = [];
        foreach ($imagePaths as $index => $path) {
            $images[] = new MasonryImageData(
                imageUrl: $this->validateImagePath($path),
                altText: $this->generateAltText(basename($path)),
                sizeClass: $sizeClasses[$index] ?? 'masonry-medium',
            );
        }

        return $images;
    }


    /**
     * Generates alt text from filename.
     */
    private function generateAltText(string $filename): string
    {
        // Remove extension and convert to readable text
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        return ucfirst($name) . ' - Storytelling moment';
    }

    /**
     * Creates a placeholder image for empty slots.
     */
    private function createPlaceholderImage(): MasonryImageData
    {
        return new MasonryImageData(
            imageUrl: self::DEFAULT_IMAGE,
            altText: 'Storytelling moment',
            sizeClass: 'masonry-medium',
        );
    }


    /**
     * Validates an image path. Returns default if invalid.
     */
    private function validateImagePath(string $path): string
    {
        if (empty($path)) {
            return self::DEFAULT_IMAGE;
        }

        if (!str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }

    /**
     * Gets a string value from content array with default fallback.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * Builds the schedule section with events grouped by day.
     */
    private function buildScheduleSection(): ScheduleSectionViewModel
    {
        $cmsContent = $this->cmsService->getSectionContent('storytelling', 'schedule_section');

        // Get visible days for storytelling event type (4)
        $visibleDays = $this->cmsEventsService->getVisibleDays(4);
        $scheduleData = $this->eventSessionRepository->findStorytellingScheduleData($visibleDays);

        $title = $this->getStringValue($cmsContent, 'schedule_title', '');
        $year = $this->getStringValue($cmsContent, 'schedule_year', '');
        $filtersButtonText = $this->getStringValue($cmsContent, 'schedule_filters_button_text', '');
        $showFilters = ($cmsContent['schedule_show_filters'] ?? '1') === '1';
        $additionalInfoTitle = $this->getStringValue($cmsContent, 'schedule_additional_info_title', '');
        $additionalInfoBody = $cmsContent['schedule_additional_info_body'] ?? '';
        $showAdditionalInfo = ($cmsContent['schedule_show_additional_info'] ?? '1') === '1';
        $eventCountLabel = $this->getStringValue($cmsContent, 'schedule_story_count_label', '');
        $showEventCount = ($cmsContent['schedule_show_story_count'] ?? '1') === '1';
        $ctaButtonText = $this->getStringValue($cmsContent, 'schedule_cta_button_text', '');
        $payWhatYouLikeText = $this->getStringValue($cmsContent, 'schedule_pay_what_you_like_text', '');
        $currencySymbol = $this->getStringValue($cmsContent, 'schedule_currency_symbol', '');
        $noEventsText = $this->getStringValue($cmsContent, 'schedule_no_events_text', '');

        $days = $this->buildScheduleDays(
            $scheduleData,
            $ctaButtonText,
            $payWhatYouLikeText,
            $currencySymbol
        );

        $eventCount = array_sum(array_map(fn($day) => count($day->events), $days));

        return new ScheduleSectionViewModel(
            sectionId: 'storytelling-schedule',
            title: $title,
            year: $year,
            eventTypeSlug: 'storytelling',
            eventTypeId: 4,
            filtersButtonText: $filtersButtonText,
            showFilters: $showFilters,
            additionalInfoTitle: $additionalInfoTitle,
            additionalInfoBody: $additionalInfoBody,
            showAdditionalInfo: $showAdditionalInfo,
            eventCountLabel: $eventCountLabel,
            eventCount: $eventCount,
            showEventCount: $showEventCount,
            ctaButtonText: $ctaButtonText,
            payWhatYouLikeText: $payWhatYouLikeText,
            currencySymbol: $currencySymbol,
            noEventsText: $noEventsText,
            days: $days,
        );
    }

    /**
     * Builds day ViewModels from schedule data.
     */
    private function buildScheduleDays(
        array  $scheduleData,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol
    ): array
    {
        $days = $scheduleData['days'] ?? [];
        $sessions = $scheduleData['sessions'] ?? [];

        if (empty($days)) {
            return [];
        }

        // Get session IDs for batch loading labels and prices
        $sessionIds = array_column($sessions, 'EventSessionId');
        $labelsMap = !empty($sessionIds) ? $this->labelRepository->findBySessionIds($sessionIds) : [];
        $pricesMap = !empty($sessionIds) ? $this->priceRepository->findBySessionIds($sessionIds) : [];

        // Group sessions by date
        $sessionsByDate = [];
        foreach ($sessions as $session) {
            $date = $session['SessionDate'];
            if (!isset($sessionsByDate[$date])) {
                $sessionsByDate[$date] = [];
            }
            $sessionsByDate[$date][] = $session;
        }

        // Build day ViewModels
        $dayViewModels = [];
        foreach ($days as $day) {
            $date = $day['Date'];
            $dateObj = new \DateTimeImmutable($date);
            $daySessions = $sessionsByDate[$date] ?? [];

            $events = [];
            foreach ($daySessions as $session) {
                $events[] = $this->buildEventCard(
                    $session,
                    $labelsMap,
                    $pricesMap,
                    $defaultCtaText,
                    $payWhatYouLikeText,
                    $currencySymbol
                );
            }

            $dayViewModels[] = new ScheduleDayViewModel(
                dayName: $dateObj->format('l'),
                dateFormatted: $dateObj->format('l, F j'),
                isoDate: $date,
                events: $events,
                isEmpty: empty($events),
            );
        }

        return $dayViewModels;
    }

    /**
     * Builds an event card ViewModel from session data.
     */
    private function buildEventCard(
        array  $session,
        array  $labelsMap,
        array  $pricesMap,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol
    ): ScheduleEventCardViewModel
    {
        $sessionId = (int)$session['EventSessionId'];
        $eventId = (int)($session['EventId'] ?? 0);
        $startDateTime = new \DateTimeImmutable($session['StartDateTime']);
        $endDateTime = $session['EndDateTime'] ? new \DateTimeImmutable($session['EndDateTime']) : null;

        // Get labels for this session
        $sessionLabels = $labelsMap[$sessionId] ?? [];
        $labels = array_map(fn($l) => $l['LabelText'], $sessionLabels);

        // Get price display
        $sessionPrices = $pricesMap[$sessionId] ?? [];
        $priceResult = $this->getPriceDisplay($sessionPrices, $payWhatYouLikeText, $currencySymbol);

        // CTA label: use session-specific if set, otherwise default
        $ctaLabel = !empty($session['CtaLabel']) ? $session['CtaLabel'] : $defaultCtaText;
        $ctaUrl = $session['CtaUrl'] ?? '#';

        // Calculate available seats for jazz
        $capacityTotal = (int)($session['CapacityTotal'] ?? 0);
        $soldTickets = (int)($session['SoldSingleTickets'] ?? 0) + (int)($session['SoldReservedSeats'] ?? 0);
        $seatsAvailable = max(0, $capacityTotal - $soldTickets);

        $eventTypeSlug = $session['EventTypeSlug'] ?? 'storytelling';
        $eventTypeId = (int)($session['EventTypeId'] ?? 4);

        return new ScheduleEventCardViewModel(
            eventSessionId: $sessionId,
            eventId: $eventId,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            title: $session['EventTitle'] ?? '',
            priceDisplay: $priceResult['display'],
            isPayWhatYouLike: $priceResult['isPayWhatYouLike'],
            ctaLabel: $ctaLabel,
            ctaUrl: $ctaUrl,
            locationName: $session['VenueName'] ?? '',
            hallName: $session['HallName'] ?? '',
            dateDisplay: $startDateTime->format('l, F j'),
            isoDate: $startDateTime->format('Y-m-d'),
            timeDisplay: $endDateTime
                ? $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i')
                : $startDateTime->format('H:i'),
            startTimeIso: $startDateTime->format('H:i'),
            endTimeIso: $endDateTime ? $endDateTime->format('H:i') : '',
            labels: $labels,
            capacityTotal: $capacityTotal,
            seatsAvailable: $seatsAvailable,
            artistName: $session['ArtistName'] ?? null,
            artistImageUrl: $session['ArtistImageUrl'] ?? null,
        );
    }

    /**
     * Determines price display text.
     * Priority: PayWhatYouLike tier → Adult tier → first available → empty
     */
    private function getPriceDisplay(array $prices, string $payWhatYouLikeText, string $currencySymbol): array
    {
        // Check for PayWhatYouLike tier first
        foreach ($prices as $price) {
            if ((int)$price['PriceTierId'] === self::PRICE_TIER_PAY_WHAT_YOU_LIKE) {
                return ['display' => $payWhatYouLikeText, 'isPayWhatYouLike' => true];
            }
        }

        // Check for Adult tier
        foreach ($prices as $price) {
            if ((int)$price['PriceTierId'] === self::PRICE_TIER_ADULT) {
                return [
                    'display' => $this->formatPrice((float)$price['Price'], $currencySymbol),
                    'isPayWhatYouLike' => false,
                ];
            }
        }

        // Fallback to first available price
        if (!empty($prices)) {
            $price = $prices[0];
            return [
                'display' => $this->formatPrice((float)$price['Price'], $currencySymbol),
                'isPayWhatYouLike' => false,
            ];
        }

        return ['display' => '', 'isPayWhatYouLike' => false];
    }

    /**
     * Formats a price with currency symbol.
     */
    private function formatPrice(float $amount, string $currencySymbol): string
    {
        return $currencySymbol . ' ' . number_format($amount, 2);
    }
}

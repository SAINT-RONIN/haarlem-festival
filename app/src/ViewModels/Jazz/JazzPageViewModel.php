<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the Jazz page.
 */
final readonly class JazzPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData       $gradientSection,
        public IntroSplitSectionData     $introSplitSection,
        public VenuesData                $venuesData,
        public PricingData               $pricingData,
        public ScheduleCallToActionData  $scheduleCtaData,
        public ArtistsData               $artistsData,
        public ScheduleData              $scheduleData,
        public BookingCallToActionData   $bookingCtaData,
        public ?ScheduleSectionViewModel $scheduleSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromData(array $data): self
    {
        $hero = $data['heroData'] ?? [];
        $globalUi = $data['globalUi'] ?? [];
        $gradient = $data['gradientSection'] ?? [];
        $intro = $data['introSplitSection'] ?? [];
        $venues = $data['venuesData'] ?? [];
        $pricing = $data['pricingData'] ?? [];
        $scheduleCta = $data['scheduleCtaData'] ?? [];
        $artists = $data['artistsData'] ?? [];
        $schedule = $data['scheduleData'] ?? [];
        $booking = $data['bookingCtaData'] ?? [];
        $scheduleSectionData = $data['scheduleSectionData'] ?? null;

        return new self(
            heroData: new HeroData(...$hero),
            globalUi: new GlobalUiData(...$globalUi),
            gradientSection: new GradientSectionData(...$gradient),
            introSplitSection: new IntroSplitSectionData(...$intro),
            venuesData: self::mapVenuesData($venues),
            pricingData: self::mapPricingData($pricing),
            scheduleCtaData: new ScheduleCallToActionData(...$scheduleCta),
            artistsData: self::mapArtistsData($artists),
            scheduleData: self::mapScheduleData($schedule),
            bookingCtaData: new BookingCallToActionData(...$booking),
            scheduleSection: is_array($scheduleSectionData)
                ? ScheduleSectionViewModel::fromData($scheduleSectionData)
                : null,
        );
    }

    /** @param array<string, mixed> $data */
    private static function mapVenuesData(array $data): VenuesData
    {
        $venues = [];
        foreach (($data['venues'] ?? []) as $venue) {
            $halls = [];
            foreach (($venue['halls'] ?? []) as $hall) {
                $halls[] = new HallData(...$hall);
            }

            $venue['halls'] = $halls;
            $venues[] = new VenueData(...$venue);
        }

        $data['venues'] = $venues;
        return new VenuesData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapPricingData(array $data): PricingData
    {
        $cards = [];
        foreach (($data['pricingCards'] ?? []) as $card) {
            $cards[] = new PricingCardData(...$card);
        }

        $data['pricingCards'] = $cards;
        return new PricingData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapArtistsData(array $data): ArtistsData
    {
        $artists = [];
        foreach (($data['artists'] ?? []) as $artist) {
            $artists[] = new ArtistCardData(...$artist);
        }

        $data['artists'] = $artists;
        return new ArtistsData(...$data);
    }

    /** @param array<string, mixed> $data */
    private static function mapScheduleData(array $data): ScheduleData
    {
        $days = [];
        foreach (($data['days'] ?? []) as $day) {
            $events = [];
            foreach (($day['events'] ?? []) as $event) {
                $events[] = new ScheduleEventData(...$event);
            }

            $day['events'] = $events;
            $days[] = new ScheduleDayData(...$day);
        }

        $data['days'] = $days;
        return new ScheduleData(...$data);
    }
}

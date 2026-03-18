<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Utils\HomeUiConfig;
use App\ViewModels\HomePageViewModel;

final class HomeMapper
{
    public static function toPageViewModel(array $data, bool $isLoggedIn): HomePageViewModel
    {
        $heroData = CmsMapper::toHeroData($data['heroContent'], 'home');
        $globalUi = CmsMapper::toGlobalUiData($data['globalUiContent'], $isLoggedIn);

        return new HomePageViewModel(
            heroData:     $heroData,
            globalUi:     $globalUi,
            cms:          CmsMapper::toCmsData($heroData, $globalUi),
            eventTypes:   $data['eventTypes'],
            locations:    $data['locations'],
            scheduleDays: self::formatScheduleDays($data['scheduleDays']),
            cmsContent:   $data['cmsContent'],
        );
    }

    /**
     * Formats raw schedule day data from the service into display-ready values.
     */
    private static function formatScheduleDays(array $rawDays): array
    {
        $formatted = [];
        foreach ($rawDays as $day) {
            $formatted[] = self::formatSingleDay($day);
        }
        return $formatted;
    }

    private static function formatSingleDay(array $day): array
    {
        $dateObj = new \DateTimeImmutable($day['date']);

        return [
            'date'       => $day['date'],
            'dayName'    => $dateObj->format('l'),
            'dayNumber'  => $dateObj->format('j'),
            'monthShort' => strtoupper($dateObj->format('M')),
            'isoDate'    => $dateObj->format('Y-m-d'),
            'eventCount' => $day['eventCount'],
            'sessions'   => self::formatSessions($day['sessions']),
        ];
    }

    private static function formatSessions(array $rawSessions): array
    {
        $formatted = [];
        foreach ($rawSessions as $session) {
            $startTime = date('H:i', $session['earliestStart']);
            $endTime   = date('H:i', $session['latestEnd']);
            $slug      = $session['eventTypeSlug'];

            $formatted[] = [
                'timeLabel'     => "{$startTime} \u{2013} {$endTime}",
                'title'         => HomeUiConfig::EVENT_SUMMARY_TITLES[$slug] ?? $session['firstEventTitle'],
                'categoryLabel' => $session['typeName'],
                'borderClass'   => HomeUiConfig::SCHEDULE_COLORS[$slug] ?? 'bg-gray-500',
            ];
        }
        return $formatted;
    }
}

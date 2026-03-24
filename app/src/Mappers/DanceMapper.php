<?php

declare(strict_types=1);

namespace App\Mappers;

final class DanceMapper
{
    public static function toPageViewModel(array $data): array
    {
        return [
            'heroData' => [
                'mainTitle' => $data['hero']['title'],
                'subtitle' => $data['hero']['subtitle'],
                'primaryButtonText' => $data['hero']['primaryButtonText'],
                'primaryButtonLink' => $data['hero']['primaryButtonLink'],
                'secondaryButtonText' => $data['hero']['secondaryButtonText'],
                'secondaryButtonLink' => $data['hero']['secondaryButtonLink'],
                'backgroundImageUrl' => $data['hero']['backgroundImage'],
            ],
            'globalUi' => [
                'siteName' => 'Haarlem Festival',
                'navHome' => 'Home',
                'navJazz' => 'Jazz',
                'navDance' => 'Dance',
                'navHistory' => 'History',
                'navRestaurant' => 'Restaurant',
                'navStorytelling' => 'Storytelling',
                'btnMyProgram' => 'My Program',
                'isLoggedIn' => false,
            ],
            'gradientSection' => [
                'headingText' => $data['gradient']['heading'],
                'subheadingText' => $data['gradient']['subheading'],
                'backgroundImageUrl' => $data['gradient']['backgroundImage'],
            ],
            'introSplitSection' => [
                'headingText' => $data['intro']['heading'],
                'bodyText' => $data['intro']['body'],
                'imageUrl' => $data['intro']['image'],
                'imageAltText' => $data['intro']['imageAlt'],
            ],
            'artists' => $data['artists'],
        ];
    }

    public static function toDetailViewModel(array $artist): array
    {
        return $artist;
    }
}
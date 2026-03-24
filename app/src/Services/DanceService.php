<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IDanceService;

final class DanceService implements IDanceService
{
    public function getDancePageData(): array
    {
        return [
            'heroData' => [
                'mainTitle' => 'HAARLEM DANCE',
                'subtitle' => "Experience high-energy dance performances at Haarlem's premier music festival. Discover our complete lineup, detailed schedules, and venue information.",
                'primaryButtonText' => 'Discover all performances',
                'primaryButtonLink' => '/dance#artists',
                'secondaryButtonText' => 'What is Haarlem Dance?',
                'secondaryButtonLink' => '/dance#about',
                'backgroundImageUrl' => '/assets/Image/Image (Dance).png',
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
                'headingText' => 'Every beat carries energy, movement, and connection beyond what is heard.',
                'subheadingText' => 'A place where dance is experienced, not just played.',
                'backgroundImageUrl' => '/assets/Image/dance/banner.jpg',
            ],
            'introSplitSection' => [
                'headingText' => 'Move to the rhythm of Haarlem Dance',
                'bodyText' => "Haarlem Dance 2026 brings together electronic music, unforgettable artists, and vibrant performances across the city. From high-energy sets to immersive festival moments, the program offers something for every dance music lover.\n\nExplore detailed information about our featured artists, performance schedules, venues, and ticket options.",
                'imageUrl' => '/assets/Image/dance/dance.jpg',
                'imageAltText' => 'Dance festival performance',
            ],
            'artists' => [
                [
                    'name' => 'Hardwell',
                    'slug' => 'hardwell',
                    'genre' => 'Dance · EDM',
                    'image' => '/assets/Image/dance/hardwell.jpg',
                    'description' => 'Known for massive live sets and high-energy festival performances.',
                ],
                [
                    'name' => 'Armin van Buuren',
                    'slug' => 'armin-van-buuren',
                    'genre' => 'Trance · Techno',
                    'image' => '/assets/Image/dance/armin.jpg',
                    'description' => 'A global icon of trance music with a legendary festival presence.',
                ],
            ],
        ];
    }

    public function getArtistDetailBySlug(string $slug): ?array
    {
        $artists = [
            'hardwell' => [
                'name' => 'Hardwell',
                'slug' => 'hardwell',
                'genre' => 'Dance · EDM',
                'heroImage' => '/assets/Image/dance/hardwell.jpg',
                'bio' => 'Hardwell is one of the most recognizable names in electronic dance music, known for explosive live shows and powerful festival sets.',
                'highlights' => [
                    'Ranked #1 DJ in the world multiple times.',
                    'Founder of Revealed Recordings.',
                    'Known for combining melodic energy with technical production.',
                ],
                'albums' => [
                    ['title' => 'United We Are', 'image' => '/assets/Image/dance/hardwell-album.jpg'],
                    ['title' => 'Rebels Never Die', 'image' => '/assets/Image/dance/hardwell-album-2.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Friday', 'time' => '20:00', 'venue' => 'Patronaat', 'price' => '€29.50'],
                    ['day' => 'Saturday', 'time' => '22:30', 'venue' => 'Main Stage', 'price' => '€35.00'],
                ],
            ],
            'armin-van-buuren' => [
                'name' => 'Armin van Buuren',
                'slug' => 'armin-van-buuren',
                'genre' => 'Trance · Techno',
                'heroImage' => '/assets/Image/dance/armin.jpg',
                'bio' => 'Armin van Buuren is a defining artist in trance and dance music, famous for his A State of Trance legacy and world-class performances.',
                'highlights' => [
                    'Founder of A State of Trance.',
                    'Award-winning producer and DJ.',
                    'Global ambassador of trance music.',
                ],
                'albums' => [
                    ['title' => 'Shivers', 'image' => '/assets/Image/dance/armin-album.jpg'],
                    ['title' => '76', 'image' => '/assets/Image/dance/armin-album-2.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Friday', 'time' => '21:30', 'venue' => 'Patronaat', 'price' => '€32.50'],
                    ['day' => 'Sunday', 'time' => '19:00', 'venue' => 'Main Stage', 'price' => '€30.00'],
                ],
            ],
        ];

        return $artists[$slug] ?? null;
    }
}
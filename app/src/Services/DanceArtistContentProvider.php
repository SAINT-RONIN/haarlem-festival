<?php

declare(strict_types=1);

namespace App\Services;

final class DanceArtistContentProvider
{
    public function getAll(): array
    {
        return [
            [
                'name' => 'Hardwell',
                'slug' => 'hardwell',
                'genre' => 'Dance | EDM',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'Live performance clip',
                    'Festival crowd moment',
                ],
                'highlights' => [
                    'He was ranked #1 DJ in the world by DJ Mag Top 100 DJs.',
                    'He is the founder of Revealed Recordings.',
                    'Known for his high-energy live sets and melodic drops.',
                ],
                'albums' => [
                    ['title' => 'United We Are', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => 'Rebels Never Die', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Friday', 'time' => '20:00', 'venue' => 'XO the Club', 'price' => '€29.50'],
                    ['day' => 'Saturday', 'time' => '22:30', 'venue' => 'Slachthuis', 'price' => '€35.00'],
                ],
                'description' => 'Known for massive live sets and high-energy festival performances.',
                'bio' => 'Hardwell is one of the most recognizable names in electronic dance music, known for explosive live shows and powerful festival sets.',
            ],
            [
                'name' => 'Armin van Buuren',
                'slug' => 'armin-van-buuren',
                'genre' => 'Trance | Techno',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'Main stage clip',
                    'Crowd energy moment',
                ],
                'highlights' => [
                    'He founded the iconic A State of Trance brand.',
                    'Armin has released multiple platinum singles.',
                    'He continues to innovate through his label Armada Music.',
                ],
                'albums' => [
                    ['title' => 'Shivers', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => '76', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Friday', 'time' => '21:30', 'venue' => 'Caprera Openluchttheater', 'price' => '€32.50'],
                    ['day' => 'Sunday', 'time' => '19:00', 'venue' => 'Jopenkerk', 'price' => '€30.00'],
                ],
                'description' => 'A global icon of trance music with a legendary festival presence.',
                'bio' => 'Armin van Buuren is a defining artist in trance and dance music, famous for his A State of Trance legacy and world-class performances.',
            ],
            [
                'name' => 'Martin Garrix',
                'slug' => 'martin-garrix',
                'genre' => 'Dance | Electronic',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'Festival anthem clip',
                    'Crowd shot',
                ],
                'highlights' => [
                    'One of the youngest globally recognized EDM headliners.',
                    'Known for major festival anthems.',
                    'Performs on major international stages.',
                ],
                'albums' => [
                    ['title' => 'Sentio', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => 'IDEM', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Saturday', 'time' => '21:00', 'venue' => 'Slachthuis', 'price' => '€34.50'],
                ],
                'description' => 'A globally celebrated producer known for energetic festival anthems.',
                'bio' => 'Martin Garrix is known worldwide for major dance hits and crowd-moving festival performances.',
            ],
            [
                'name' => 'Tiësto',
                'slug' => 'tiesto',
                'genre' => 'Trance | Techno | Minimal | House',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'Arena clip',
                    'Festival lights moment',
                ],
                'highlights' => [
                    'One of the most iconic EDM artists worldwide.',
                    'Known for massive main-stage performances.',
                    'Long-standing influence on dance music culture.',
                ],
                'albums' => [
                    ['title' => 'Drive', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => 'A Town Called Paradise', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Saturday', 'time' => '23:00', 'venue' => 'Lichtfabriek', 'price' => '€36.00'],
                ],
                'description' => 'A legendary dance artist known for iconic live sets.',
                'bio' => 'Tiësto is one of the most influential names in electronic music and a festival favourite around the world.',
            ],
            [
                'name' => 'Nicky Romero',
                'slug' => 'nicky-romero',
                'genre' => 'Electrohouse | Progressive House',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'Progressive house set clip',
                    'Crowd reaction shot',
                ],
                'highlights' => [
                    'Recognized for festival-ready progressive house sets.',
                    'Popular international dance music producer.',
                    'Frequent performer on major dance stages.',
                ],
                'albums' => [
                    ['title' => 'Redefine', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => 'Protocol Highlights', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Friday', 'time' => '19:30', 'venue' => 'Patronaat', 'price' => '€24.50'],
                ],
                'description' => 'Known for progressive house energy and crowd-driven performances.',
                'bio' => 'Nicky Romero is a major name in electrohouse and progressive house with a strong festival reputation.',
            ],
            [
                'name' => 'Afrojack',
                'slug' => 'afrojack',
                'genre' => 'House',
                'heroImage' => '/assets/Image/dance/dance.jpg',
                'gallery' => [
                    '/assets/Image/dance/dance.jpg',
                    '/assets/Image/dance/banner.jpg',
                    '/assets/Image/Image (Dance).png',
                ],
                'videos' => [
                    'House festival clip',
                    'Main stage crowd moment',
                ],
                'highlights' => [
                    'Internationally recognized house music artist.',
                    'Known for crowd-pleasing live performances.',
                    'Strong influence on modern festival sound.',
                ],
                'albums' => [
                    ['title' => 'Forget the World', 'image' => '/assets/Image/dance/dance.jpg'],
                    ['title' => 'Press Play', 'image' => '/assets/Image/dance/banner.jpg'],
                ],
                'schedule' => [
                    ['day' => 'Sunday', 'time' => '20:00', 'venue' => 'Lichtfabriek', 'price' => '€27.00'],
                ],
                'description' => 'A major house music artist known for energetic performances.',
                'bio' => 'Afrojack is an internationally known DJ and producer with strong roots in house and festival music.',
            ],
        ];
    }

    public function getBySlug(string $slug): ?array
    {
        foreach ($this->getAll() as $artist) {
            if ($artist['slug'] === $slug) {
                return $artist;
            }
        }

        return null;
    }
}
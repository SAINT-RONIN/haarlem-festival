<?php

declare(strict_types=1);

namespace App\Services;

use App\ViewModels\Jazz\JazzArtistAlbumData;
use App\ViewModels\Jazz\JazzArtistDetailPageViewModel;
use App\ViewModels\Jazz\JazzArtistTrackData;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * Hardcoded content provider for Jazz artist detail pages.
 *
 * This is temporary content until CMS integration is added.
 */
class JazzArtistDetailService
{
    public function getGumboKingsPageData(): JazzArtistDetailPageViewModel
    {
        return new JazzArtistDetailPageViewModel(
            heroTitle: 'Gumbo Kings',
            heroSubtitle: 'New Orleans Jazz',
            heroBackgroundImageUrl: '/assets/Image/Jazz/GubmboKings-Hero.png',
            originText: 'Origin: New Orleans, Louisiana, USA',
            formedText: 'Formed: 2015',
            performancesText: '2 performances at Haarlem Jazz 2024',
            overviewHeading: 'Gumbo Kings',
            overviewLead: 'High-energy New Orleans style jazz band bringing authentic Big Easy sound to Haarlem. Known for their infectious rhythms and crowd-pleasing performances that get audiences on their feet.',
            overviewBodyPrimary: 'The Gumbo Kings deliver an electrifying blend of traditional New Orleans jazz, funk, and second-line grooves. With a powerful horn section, driving rhythm section, and authentic Crescent City soul, they transport audiences straight to the streets of the French Quarter. Their performances are known for spontaneous moments of musical magic and infectious energy that keeps crowds dancing all night long. Since their formation in 2015, they have become one of the most sought-after New Orleans jazz acts in Europe.',
            overviewBodySecondary: 'Drawing inspiration from the rich musical heritage of New Orleans, the Gumbo Kings have mastered the art of combining traditional jazz elements with contemporary energy. Their repertoire spans classic jazz standards, original compositions, and reimagined funk grooves. The band has performed at major jazz festivals across Europe and the United States, earning acclaim for their authentic sound and dynamic stage presence. Their commitment to preserving and evolving the New Orleans jazz tradition has made them favorites among both purists and new jazz audiences.',
            lineupHeading: 'Band Lineup',
            lineup: [
                'Marcus Johnson - Trumpet, Band Leader',
                'DeShawn Williams - Trombone',
                'Antoine Davis - Tenor Saxophone',
                'Jerome Baptiste - Drums',
                'Louis Carter - Upright Bass',
                'Raymond Pierce - Piano',
            ],
            highlightsHeading: 'Career Highlights',
            highlights: [
                'Featured performers at New Orleans Jazz & Heritage Festival 2023',
                'Touring Europe extensively since 2019, performing at 50+ major festivals',
                'Authentic brass band sound with modern energy and innovation',
                'Collaborations with legendary New Orleans musicians including Trombone Shorty',
                'Known for interactive, high-energy live shows that get audiences dancing',
                'Winner of "Best Jazz Ensemble" at European Jazz Awards 2022',
            ],
            photoGalleryHeading: 'Photo Gallery',
            photoGalleryDescription: 'Experience the energy and passion of Gumbo Kings through these performance and portrait photographs.',
            galleryImages: [
                '/assets/Image/Jazz/GumboGallery1.png',
                '/assets/Image/Jazz/GumboGallery2.png',
                '/assets/Image/Jazz/GumboGallery3.png',
            ],
            albumsHeading: 'Featured Albums',
            albumsDescription: 'Explore the studio recordings that capture the magic of Gumbo Kings. Each album showcases their evolution and mastery of the New Orleans jazz tradition.',
            albums: [
                new JazzArtistAlbumData(
                    title: 'Second Line Swing',
                    description: 'Their breakthrough album featuring traditional second line rhythms mixed with contemporary jazz sensibilities. The title track became a festival favorite across Europe.',
                    year: '2019',
                    tag: 'JAZZ',
                    imageUrl: '/assets/Image/Jazz/GumboKingsAlbum1.png',
                ),
                new JazzArtistAlbumData(
                    title: 'Big Easy',
                    description: 'A love letter to New Orleans featuring reimagined classics and original compositions inspired by the city\'s rich musical heritage.',
                    year: '2021',
                    tag: 'JAZZ',
                    imageUrl: '/assets/Image/Jazz/GumboKingsAlbum2.png',
                ),
                new JazzArtistAlbumData(
                    title: 'Live at Paradiso',
                    description: 'Recorded live at Amsterdam\'s legendary Paradiso venue, this album captures the raw energy and spontaneity of their live performances.',
                    year: '2023',
                    tag: 'LIVE',
                    imageUrl: '/assets/Image/Jazz/GumboKingsAlbum3.png',
                ),
            ],
            listenHeading: 'LISTEN NOW',
            listenSubheading: 'Important Tracks',
            listenDescription: 'Listen to excerpts from Gumbo Kings\'s most important and popular tracks. Experience the energy and musicianship that defines their sound.',
            tracks: [
                new JazzArtistTrackData(
                    title: 'All Night Long',
                    album: 'Live in the Quarter',
                    description: 'Classic New Orleans standard with powerful brass arrangements',
                    duration: '4:32',
                    imageUrl: '/assets/Image/Jazz/Allnightlong.png',
                    progressClass: 'w-[5%]',
                ),
                new JazzArtistTrackData(
                    title: 'Hot Damn!',
                    album: 'Brass & Soul',
                    description: 'Original composition featuring traditional second-line rhythms',
                    duration: '3:45',
                    imageUrl: '/assets/Image/Jazz/Container.png',
                    progressClass: 'w-[15%]',
                ),
                new JazzArtistTrackData(
                    title: 'Valenzuela',
                    album: 'Big Easy Nights',
                    description: 'Fast-paced instrumental showcasing virtuoso musicianship',
                    duration: '4:18',
                    imageUrl: '/assets/Image/Jazz/Listennowsection.png',
                    progressClass: 'w-full',
                ),
                new JazzArtistTrackData(
                    title: 'Here We Are',
                    album: 'Live in the Quarter',
                    description: 'High-energy rendition of the jazz funeral classic',
                    duration: '5:12',
                    imageUrl: '/assets/Image/Jazz/Allnightlong.png',
                    progressClass: 'w-[60%]',
                ),
            ],
            liveCtaHeading: 'Experience Gumbo Kings Live',
            liveCtaDescription: 'Don\'t miss the chance to see Gumbo Kings perform live at Haarlem Jazz 2024. With 2 performances scheduled, there are multiple opportunities to experience their incredible energy and musicianship.',
            performancesSectionId: 'artist-performances',
            performancesHeading: 'Gumbo Kings at Haarlem Jazz 2026',
            performancesDescription: 'Catch Gumbo Kings performing 2 times during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.',
            performances: [
                $this->createPerformanceCard(
                    eventSessionId: 1001,
                    title: 'Gumbo Kings',
                    priceDisplay: '€ 15.00',
                    locationName: 'Grote Kerk - Main Stage',
                    dateDisplay: 'Thursday, July 25',
                    isoDate: '2026-07-25',
                    timeDisplay: '18:00 - 19:00',
                    startTimeIso: '18:00',
                    endTimeIso: '19:00',
                    labels: ['Jazz'],
                ),
                $this->createPerformanceCard(
                    eventSessionId: 1002,
                    title: 'Gumbo Kings',
                    priceDisplay: 'Free',
                    locationName: 'Grote Markt - Open Air Stage',
                    dateDisplay: 'Sunday, July 28',
                    isoDate: '2026-07-28',
                    timeDisplay: '19:00 - 20:00',
                    startTimeIso: '19:00',
                    endTimeIso: '20:00',
                    labels: ['Jazz'],
                ),
            ],
        );
    }

    public function getNtjamRosiePageData(): JazzArtistDetailPageViewModel
    {
        return new JazzArtistDetailPageViewModel(
            heroTitle: 'Ntjam Rosie',
            heroSubtitle: 'Vocal Jazz',
            heroBackgroundImageUrl: '/assets/Image/Jazz/Ntjamhero.png',
            originText: 'Origin: Cameroon / Netherlands',
            formedText: 'Formed: 2008',
            performancesText: '2 performances at Haarlem Jazz 2024',
            overviewHeading: 'Ntjam Rosie',
            overviewLead: 'Ntjam Rosie was born in Cameroon on March 18, 1983, and moved to the Netherlands at the age of nine. She blends her West-African roots with Western musical traditions, combining jazz, soul, pop and Afro influences.',
            overviewBodyPrimary: 'Her debut album, Atouba, released in 2008, was the first to showcase that hybrid style, mixing African rhythms with soul and jazz influences. Since then she has developed a distinctive musical voice across several albums and performances.',
            overviewBodySecondary: 'Ntjam Rosie has built a reputation for compelling live performances and wide appeal. She has performed at major festivals and toured internationally. Her music resonates with both jazz and soul audiences, and she continues to evolve by blending tradition and innovation.',
            lineupHeading: 'Band Lineup',
            lineup: [
                'Ntjam Rosie - Vocals',
                'Bart Wirtz - Tenor Saxophone',
                'Niels Broos - Piano & Keys',
                'Bram Hakkens - Drums',
                'Tijn Wybenga - Bass',
            ],
            highlightsHeading: 'Career Highlights',
            highlights: [
                'Featured performer at the New Orleans Jazz & Heritage Festival 2023',
                'Toured across Europe since 2019, playing 50+ major festivals',
                'Blends Afro-European vocals with modern jazz and soul energy',
                'Collaborated with New Orleans artists, including Trombone Shorty',
                'Renowned for vibrant, high-energy live shows',
                'Winner of "Best Jazz Ensemble" at the European Jazz Awards 2022',
            ],
            photoGalleryHeading: 'Photo Gallery',
            photoGalleryDescription: 'Experience the soulful elegance of Ntjam Rosie through these intimate performance and portrait photographs.',
            galleryImages: [
                '/assets/Image/Jazz/Ntjamgallery1.png',
                '/assets/Image/Jazz/Ntjamgallery2.png',
                '/assets/Image/Jazz/Ntjamgallery3.png',
            ],
            albumsHeading: 'Featured Albums',
            albumsDescription: 'Explore the studio recordings that capture the soulful artistry of Ntjam Rosie. Each album reflects her evolving blend of jazz, soul, and Afro-inspired sound.',
            albums: [
                new JazzArtistAlbumData(
                    title: 'Atouba',
                    description: 'Her debut album where Ntjam Rosie introduced her Afro-European blend of soul and jazz.',
                    year: '2019',
                    tag: 'JAZZ',
                    imageUrl: '/assets/Image/Jazz/Ntjamalbum1.png',
                ),
                new JazzArtistAlbumData(
                    title: 'At the Back of Beyond',
                    description: 'At the Back of Beyond showcases Ntjam Rosie\'s soulful blend of jazz and Afro-European sounds.',
                    year: '2021',
                    tag: 'JAZZ',
                    imageUrl: '/assets/Image/Jazz/Ntjamalbum2.png',
                ),
                new JazzArtistAlbumData(
                    title: 'Family & Friends',
                    description: 'Family & Friends highlights Ntjam Rosie\'s warm vocals and her fusion of jazz and Afro-European influences.',
                    year: '2023',
                    tag: 'LIVE',
                    imageUrl: '/assets/Image/Jazz/Ntjamalbum3.png',
                ),
            ],
            listenHeading: 'LISTEN NOW',
            listenSubheading: 'Important Tracks',
            listenDescription: 'Listen to excerpts from Ntjam Rosie\'s most celebrated and influential tracks. Immerse yourself in the soulful energy and refined musicianship that define her signature sound.',
            tracks: [
                new JazzArtistTrackData(
                    title: 'What is Love?',
                    album: 'Live in the Quarter',
                    description: 'Classic New Orleans standard with powerful brass arrangements',
                    duration: '4:32',
                    imageUrl: '/assets/Image/Jazz/Ntjamwhatislove.png',
                    progressClass: 'w-[5%]',
                ),
                new JazzArtistTrackData(
                    title: 'Thinkin About You',
                    album: 'Brass & Soul',
                    description: 'Original composition featuring traditional second-line rhythms',
                    duration: '3:45',
                    imageUrl: '/assets/Image/Jazz/Ntjamthinkinaboutyou.png',
                    progressClass: 'w-[15%]',
                ),
                new JazzArtistTrackData(
                    title: 'You got this',
                    album: 'Big Easy Nights',
                    description: 'Fast-paced instrumental showcasing virtuoso musicianship',
                    duration: '4:18',
                    imageUrl: '/assets/Image/Jazz/Ntjamyougotthis.png',
                    progressClass: 'w-full',
                ),
                new JazzArtistTrackData(
                    title: 'In Need - Reworked',
                    album: 'Live in the Quarter',
                    description: 'High-energy rendition of the jazz funeral classic',
                    duration: '5:12',
                    imageUrl: '/assets/Image/Jazz/Ntjaminneed.png',
                    progressClass: 'w-[60%]',
                ),
            ],
            liveCtaHeading: 'Experience Ntjam Rosie Live',
            liveCtaDescription: 'Do not miss the chance to see Ntjam Rosie perform live at Haarlem Jazz 2026. With 2 performances scheduled, there are multiple opportunities to experience her incredible energy and musicianship.',
            performancesSectionId: 'artist-performances',
            performancesHeading: 'Ntjam Rosie at Haarlem Jazz 2026',
            performancesDescription: 'Catch Ntjam Rosie performing 2 times during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.',
            performances: [
                $this->createPerformanceCard(
                    eventSessionId: 2001,
                    title: 'Ntjam Rosie',
                    priceDisplay: '€ 15.00',
                    locationName: 'Patronaat - Main Hall',
                    dateDisplay: 'Thursday, July 25',
                    isoDate: '2026-07-25',
                    timeDisplay: '21:00 - 22:00',
                    startTimeIso: '21:00',
                    endTimeIso: '22:00',
                    labels: ['Soul'],
                ),
                $this->createPerformanceCard(
                    eventSessionId: 2002,
                    title: 'Ntjam Rosie',
                    priceDisplay: 'Free',
                    locationName: 'Grote Markt - Open Air Stage',
                    dateDisplay: 'Sunday, July 28',
                    isoDate: '2026-07-28',
                    timeDisplay: '19:30 - 20:30',
                    startTimeIso: '19:30',
                    endTimeIso: '20:30',
                    labels: ['Soul'],
                ),
            ],
        );
    }

    /**
     * @param array<string> $labels
     */
    private function createPerformanceCard(
        int $eventSessionId,
        string $title,
        string $priceDisplay,
        string $locationName,
        string $dateDisplay,
        string $isoDate,
        string $timeDisplay,
        string $startTimeIso,
        string $endTimeIso,
        array $labels,
    ): ScheduleEventCardViewModel {
        return new ScheduleEventCardViewModel(
            eventSessionId: $eventSessionId,
            eventId: $eventSessionId,
            eventTypeSlug: 'jazz',
            eventTypeId: 1,
            title: $title,
            priceDisplay: $priceDisplay,
            isPayWhatYouLike: false,
            ctaLabel: 'Get Tickets',
            ctaUrl: '#',
            locationName: $locationName,
            hallName: '',
            dateDisplay: $dateDisplay,
            isoDate: $isoDate,
            timeDisplay: $timeDisplay,
            startTimeIso: $startTimeIso,
            endTimeIso: $endTimeIso,
            labels: $labels,
        );
    }
}

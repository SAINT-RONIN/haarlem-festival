<?php

declare(strict_types=1);

namespace App\Services;

use App\ViewModels\Jazz\GumboKingsAlbumData;
use App\ViewModels\Jazz\GumboKingsDetailPageViewModel;
use App\ViewModels\Jazz\GumboKingsTrackData;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * Hardcoded content provider for Gumbo Kings detail page.
 * Content will be replaced by CMS/database integration in a next phase.
 */
class GumboKingsDetailService
{
    public function getPageData(): GumboKingsDetailPageViewModel
    {
        return new GumboKingsDetailPageViewModel(
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
            lineup: $this->buildLineup(),
            highlights: $this->buildHighlights(),
            galleryImages: $this->buildGalleryImages(),
            albums: $this->buildAlbums(),
            albumsDescription: 'Explore the studio recordings that capture the magic of Gumbo Kings. Each album showcases their evolution and mastery of the New Orleans jazz tradition.',
            tracks: $this->buildTracks(),
            listenHeading: 'LISTEN NOW',
            listenSubheading: 'Important Tracks',
            listenDescription: 'Listen to excerpts from Gumbo Kings\'s most important and popular tracks. Experience the energy and musicianship that defines their sound.',
            liveCtaHeading: 'Experience Gumbo Kings Live',
            liveCtaDescription: 'Don\'t miss the chance to see Gumbo Kings perform live at Haarlem Jazz 2024. With 2 performances scheduled, there are multiple opportunities to experience their incredible energy and musicianship.',
            performancesHeading: 'Gumbo Kings at Haarlem Jazz 2026',
            performancesDescription: 'Catch Gumbo Kings performing 2 times during the Haarlem Jazz Festival. Each performance offers a unique experience from intimate indoor shows to free outdoor concerts.',
            performances: $this->buildPerformances(),
        );
    }

    /**
     * @return array<string>
     */
    private function buildLineup(): array
    {
        return [
            'Marcus Johnson - Trumpet, Band Leader',
            'DeShawn Williams - Trombone',
            'Antoine Davis - Tenor Saxophone',
            'Jerome Baptiste - Drums',
            'Louis Carter - Upright Bass',
            'Raymond Pierce - Piano',
        ];
    }

    /**
     * @return array<string>
     */
    private function buildHighlights(): array
    {
        return [
            'Featured performers at New Orleans Jazz & Heritage Festival 2023',
            'Touring Europe extensively since 2019, performing at 50+ major festivals',
            'Authentic brass band sound with modern energy and innovation',
            'Collaborations with legendary New Orleans musicians including Trombone Shorty',
            'Known for interactive, high-energy live shows that get audiences dancing',
            'Winner of "Best Jazz Ensemble" at European Jazz Awards 2022',
        ];
    }

    /**
     * @return array<string>
     */
    private function buildGalleryImages(): array
    {
        return [
            '/assets/Image/Jazz/GumboGallery1.png',
            '/assets/Image/Jazz/GumboGallery2.png',
            '/assets/Image/Jazz/GumboGallery3.png',
        ];
    }

    /**
     * @return array<GumboKingsAlbumData>
     */
    private function buildAlbums(): array
    {
        return [
            new GumboKingsAlbumData(
                title: 'Second Line Swing',
                description: 'Their breakthrough album featuring traditional second line rhythms mixed with contemporary jazz sensibilities. The title track became a festival favorite across Europe.',
                year: '2019',
                tag: 'JAZZ',
                imageUrl: '/assets/Image/Jazz/GumboKingsAlbum1.png',
            ),
            new GumboKingsAlbumData(
                title: 'Big Easy',
                description: 'A love letter to New Orleans featuring reimagined classics and original compositions inspired by the city\'s rich musical heritage.',
                year: '2021',
                tag: 'JAZZ',
                imageUrl: '/assets/Image/Jazz/GumboKingsAlbum2.png',
            ),
            new GumboKingsAlbumData(
                title: 'Live at Paradiso',
                description: 'Recorded live at Amsterdam\'s legendary Paradiso venue, this album captures the raw energy and spontaneity of their live performances.',
                year: '2023',
                tag: 'LIVE',
                imageUrl: '/assets/Image/Jazz/GumboKingsAlbum3.png',
            ),
        ];
    }

    /**
     * @return array<GumboKingsTrackData>
     */
    private function buildTracks(): array
    {
        return [
            new GumboKingsTrackData(
                title: 'All Night Long',
                album: 'Live in the Quarter',
                description: 'Classic New Orleans standard with powerful brass arrangements',
                duration: '4:32',
                imageUrl: '/assets/Image/Jazz/Allnightlong.png',
                progressClass: 'w-[5%]',
            ),
            new GumboKingsTrackData(
                title: 'Hot Damn!',
                album: 'Brass & Soul',
                description: 'Original composition featuring traditional second-line rhythms',
                duration: '3:45',
                imageUrl: '/assets/Image/Jazz/Container.png',
                progressClass: 'w-[15%]',
            ),
            new GumboKingsTrackData(
                title: 'Valenzuela',
                album: 'Big Easy Nights',
                description: 'Fast-paced instrumental showcasing virtuoso musicianship',
                duration: '4:18',
                imageUrl: '/assets/Image/Jazz/Listennowsection.png',
                progressClass: 'w-full',
            ),
            new GumboKingsTrackData(
                title: 'Here We Are',
                album: 'Live in the Quarter',
                description: 'High-energy rendition of the jazz funeral classic',
                duration: '5:12',
                imageUrl: '/assets/Image/Jazz/Allnightlong.png',
                progressClass: 'w-[60%]',
            ),
        ];
    }

    /**
     * @return array<ScheduleEventCardViewModel>
     */
    private function buildPerformances(): array
    {
        return [
            new ScheduleEventCardViewModel(
                eventSessionId: 1001,
                eventId: 1001,
                eventTypeSlug: 'jazz',
                eventTypeId: 1,
                title: 'Gumbo Kings',
                priceDisplay: '€ 15.00',
                isPayWhatYouLike: false,
                ctaLabel: 'Get Tickets',
                ctaUrl: '#',
                locationName: 'Grote Kerk - Main Stage',
                hallName: '',
                dateDisplay: 'Thursday, July 25',
                isoDate: '2026-07-25',
                timeDisplay: '18:00 - 19:00',
                startTimeIso: '18:00',
                endTimeIso: '19:00',
                labels: ['Jazz'],
            ),
            new ScheduleEventCardViewModel(
                eventSessionId: 1002,
                eventId: 1002,
                eventTypeSlug: 'jazz',
                eventTypeId: 1,
                title: 'Gumbo Kings',
                priceDisplay: 'Free',
                isPayWhatYouLike: false,
                ctaLabel: 'Get Tickets',
                ctaUrl: '#',
                locationName: 'Grote Markt - Open Air Stage',
                hallName: '',
                dateDisplay: 'Sunday, July 28',
                isoDate: '2026-07-28',
                timeDisplay: '19:00 - 20:00',
                startTimeIso: '19:00',
                endTimeIso: '20:00',
                labels: ['Jazz'],
            ),
        ];
    }
}

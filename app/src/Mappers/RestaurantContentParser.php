<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\CuisineType;
use App\Models\Restaurant;
use App\Models\RestaurantDetailSectionContent;
use App\DTOs\Pages\RestaurantDetailData;

/**
 * Extracts and normalises raw content fields for restaurant pages.
 * Called by RestaurantViewMapper during ViewModel construction.
 */
final class RestaurantContentParser
{
    public const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    /**
     * Joins cuisine type names into a comma-separated display string.
     *
     * @param CuisineType[] $cuisineTypes
     */
    public static function buildCuisineString(array $cuisineTypes): string
    {
        return implode(', ', array_map(fn(CuisineType $c) => $c->name, $cuisineTypes));
    }

    /**
     * Extracts domain-level fields from a restaurant and its related data
     * into a flat array consumed by RestaurantDetailViewModel.
     *
     * @return array<string, mixed>
     */
    public static function buildDetailDomainFields(Restaurant $restaurant, RestaurantDetailData $data, string $cuisineString): array
    {
        $cuisineTags = array_map(fn(CuisineType $c) => $c->name, $data->cuisineTypes);

        return [
            'id'          => $restaurant->restaurantId,
            'name'        => $restaurant->name,
            'cuisine'     => $cuisineString,
            'address'     => self::buildAddress($restaurant),
            'description' => self::cleanDescription($restaurant->descriptionHtml),
            'rating'      => $restaurant->stars ?? 0,
            'image'       => $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            'phone'       => $restaurant->phone ?? '',
            'email'       => $restaurant->email ?? '',
            'website'     => $restaurant->website ?? '',
            'aboutText'   => str_replace('\n', "\n", $restaurant->aboutText ?? ''),
            'aboutImage'  => ($data->imagesByType['about'] ?? [])[0] ?? self::DEFAULT_IMAGE,
            'chefName'    => $restaurant->chefName ?? '',
            'chefText'    => str_replace('\n', "\n", $restaurant->chefText ?? ''),
            'chefImage'   => ($data->imagesByType['chef'] ?? [])[0] ?? self::DEFAULT_IMAGE,
            'menuDescription' => $restaurant->menuDescription ?? '',
            'cuisineTags'     => $cuisineTags,
            'menuImages'      => $data->imagesByType['menu'] ?? [self::DEFAULT_IMAGE, self::DEFAULT_IMAGE],
            'locationDescription' => str_replace('\n', "\n", $restaurant->locationDescription ?? ''),
            'mapEmbedUrl'     => $restaurant->mapEmbedUrl ?? '',
            'michelinStars'   => $restaurant->michelinStars ?? 0,
            'seatsPerSession' => $restaurant->seatsPerSession ?? 0,
            'durationMinutes' => $restaurant->durationMinutes ?? 0,
            'specialRequestsNote' => $restaurant->specialRequestsNote ?? '',
            'galleryImages'   => $data->imagesByType['gallery'] ?? [self::DEFAULT_IMAGE],
            'reservationImage' => ($data->imagesByType['reservation'] ?? [])[0] ?? self::DEFAULT_IMAGE,
            'timeSlots'       => $data->timeSlots,
            'priceCards'      => $data->priceCards,
        ];
    }

    /**
     * Maps CMS detail-section fields to view-ready label keys.
     *
     * @return array<string, string>
     */
    public static function buildDetailCmsLabels(RestaurantDetailSectionContent $cms): array
    {
        return [
            'labelContactTitle'    => $cms->detailContactTitle ?? '',
            'labelAddress'         => $cms->detailLabelAddress ?? '',
            'labelContact'         => $cms->detailLabelContact ?? '',
            'labelOpenHours'       => $cms->detailLabelOpenHours ?? '',
            'labelPracticalTitle'  => $cms->detailPracticalTitle ?? '',
            'labelPriceFood'       => $cms->detailLabelPriceFood ?? '',
            'labelRating'          => $cms->detailLabelRating ?? '',
            'labelSpecialRequests' => $cms->detailLabelSpecialRequests ?? '',
            'labelGalleryTitle'    => $cms->detailGalleryTitle ?? '',
            'labelAboutPrefix'     => $cms->detailAboutTitlePrefix ?? '',
            'labelChefTitle'       => $cms->detailChefTitle ?? '',
            'labelMenuTitle'       => $cms->detailMenuTitle ?? '',
            'labelCuisineType'     => $cms->detailMenuCuisineLabel ?? '',
            'labelLocationTitle'   => $cms->detailLocationTitle ?? '',
            'labelLocationAddress' => $cms->detailLocationAddressLabel ?? '',
            'labelReservationTitle' => $cms->detailReservationTitle ?? '',
            'labelReservationDesc' => $cms->detailReservationDescription ?? '',
            'labelSlotsLabel'      => $cms->detailReservationSlotsLabel ?? '',
            'labelReservationNote' => $cms->detailReservationNote ?? '',
            'labelReservationBtn'  => $cms->detailReservationBtn ?? '',
            'labelDuration'        => $cms->detailLabelDuration ?? '',
            'labelSeats'           => $cms->detailLabelSeats ?? '',
            'labelFestivalRated'   => $cms->detailLabelFestivalRated ?? '',
            'labelMichelin'        => $cms->detailLabelMichelin ?? '',
            'labelMapFallback'     => $cms->detailMapFallbackText ?? '',
        ];
    }

    /** Strips HTML tags and normalises whitespace from a restaurant description. */
    public static function cleanDescription(string $html): string
    {
        $html = trim($html);

        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    /** Validates an image path starts with /assets/ and has a known extension. */
    public static function validateImagePath(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }

    /** Constructs a full address string from restaurant address components. */
    public static function buildAddress(Restaurant $restaurant): string
    {
        $address = trim($restaurant->addressLine);

        if ($restaurant->city !== '') {
            $address .= ', ' . $restaurant->city;
        }

        return $address;
    }

    /**
     * Parses the intro_body blob into structured components.
     *
     * Restaurant convention:
     * - First block before any "##" is bodyText
     * - Each "## Heading" becomes a subsection
     * - Final paragraph after the last subsection becomes closingLine
     */
    public static function parseIntroBody(string $rawBody): array
    {
        $rawBody = trim($rawBody);
        if ($rawBody === '') {
            return ['bodyText' => '', 'subsections' => null, 'closingLine' => null];
        }

        $blocks = self::splitIntoBlocks($rawBody);
        if ($blocks === []) {
            return ['bodyText' => $rawBody, 'subsections' => null, 'closingLine' => null];
        }

        $bodyText = self::extractBodyText($blocks, $headingStartIndex);
        $parsed = self::extractSubsections($blocks, $headingStartIndex);

        return [
            'bodyText'    => $bodyText,
            'subsections' => $parsed['subsections'] !== [] ? $parsed['subsections'] : null,
            'closingLine' => $parsed['closingLine'],
        ];
    }

    /** Normalises line endings and splits into double-newline-separated blocks. */
    private static function splitIntoBlocks(string $rawBody): array
    {
        $rawBody = str_replace(["\r\n", "\r"], "\n", $rawBody);
        $blocks = preg_split("/\n\n+/", $rawBody);

        return ($blocks !== false && $blocks !== []) ? $blocks : [];
    }

    /**
     * Collects body text before any heading block.
     * Sets $headingStartIndex to the first heading block's position.
     */
    private static function extractBodyText(array $blocks, ?int &$headingStartIndex): string
    {
        $bodyParts = [];
        $headingStartIndex = count($blocks);

        for ($i = 0; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if (str_starts_with($b, '## ')) {
                $headingStartIndex = $i;
                break;
            }
            if ($b !== '') {
                $bodyParts[] = $b;
            }
        }

        return implode("\n\n", $bodyParts);
    }

    /**
     * Collects subsections (## headings with optional body) and any trailing closing line.
     *
     * @return array{subsections: array, closingLine: ?string}
     */
    private static function extractSubsections(array $blocks, int $startIndex): array
    {
        $subsections = [];
        $closingLine = null;

        for ($i = $startIndex; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if ($b === '') {
                continue;
            }

            if (str_starts_with($b, '## ')) {
                $heading = trim(substr($b, 3));
                $text = '';

                if (($i + 1) < count($blocks)) {
                    $next = trim((string)$blocks[$i + 1]);
                    if ($next !== '' && !str_starts_with($next, '## ')) {
                        $text = $next;
                        $i++;
                    }
                }

                $subsections[] = ['heading' => $heading, 'text' => $text];
                continue;
            }

            $closingLine = $b;
        }

        return ['subsections' => $subsections, 'closingLine' => $closingLine];
    }
}

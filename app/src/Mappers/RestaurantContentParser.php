<?php

declare(strict_types=1);

namespace App\Mappers;

/**
 * Extracts and normalises raw content fields for restaurant pages.
 * Called by RestaurantViewMapper during ViewModel construction.
 */
final class RestaurantContentParser
{
    public const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

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

    private static function splitIntoBlocks(string $rawBody): array
    {
        $rawBody = str_replace(["\r\n", "\r"], "\n", $rawBody);
        $blocks = preg_split("/\n\n+/", $rawBody);

        return ($blocks !== false && $blocks !== []) ? $blocks : [];
    }

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

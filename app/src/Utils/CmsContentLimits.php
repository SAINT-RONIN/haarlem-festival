<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * Content limits configuration for CMS items.
 *
 * Defines maximum character counts for text fields and
 * dimension/size limits for image uploads to prevent
 * layout-breaking content changes.
 */
class CmsContentLimits
{
    // Text content limits (characters)
    public const HEADING_MAX_CHARS = 100;
    public const TEXT_MAX_CHARS = 1000;
    public const HTML_MAX_CHARS = 5000;
    public const BUTTON_MAX_CHARS = 50;

    // Image limits
    public const IMAGE_MAX_WIDTH = 10000;  // 4K width
    public const IMAGE_MAX_HEIGHT = 10000; // Allow tall images
    public const IMAGE_MAX_FILE_SIZE = 5242880; // 5MB in bytes
    public const IMAGE_ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Gets the character limit for a given item type.
     */
    public static function getCharLimitForType(string $itemType): int
    {
        return match (strtoupper($itemType)) {
            'HEADING' => self::HEADING_MAX_CHARS,
            'TEXT' => self::TEXT_MAX_CHARS,
            'HTML' => self::HTML_MAX_CHARS,
            'BUTTON_TEXT' => self::BUTTON_MAX_CHARS,
            default => self::TEXT_MAX_CHARS,
        };
    }

    /**
     * Gets a human-readable label for an item type.
     */
    public static function getLabelForType(string $itemType): string
    {
        return match (strtoupper($itemType)) {
            'HEADING' => 'Heading',
            'TEXT' => 'Text',
            'HTML' => 'Rich Text (HTML)',
            'BUTTON_TEXT' => 'Button Text',
            'MEDIA', 'IMAGE_PATH' => 'Image',
            'LINK' => 'Link',
            default => 'Content',
        };
    }

    /**
     * Checks if the item type uses TinyMCE.
     */
    public static function usesTinyMce(string $itemType): bool
    {
        return strtoupper($itemType) === 'HTML';
    }

    /**
     * Determines whether a TEXT item should be edited with TinyMCE based on its key.
     *
     * This prevents TinyMCE from wrapping short plain fields (addresses, labels) in <p> tags,
     * while still enabling formatting for longer descriptive content.
     */
    public static function textKeyUsesTinyMce(string $itemKey): bool
    {
        $key = strtolower($itemKey);

        $allowContains = [
            'description',
            'subtitle',
            'body',
            '_info',
        ];

        foreach ($allowContains as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the input type for a given item type.
     */
    public static function getInputType(string $itemType): string
    {
        return match (strtoupper($itemType)) {
            'HTML' => 'tinymce',
            'MEDIA', 'IMAGE_PATH' => 'file',
            default => 'text',
        };
    }
}

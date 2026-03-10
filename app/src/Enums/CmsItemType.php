<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Backed enum for CMS item types matching database CmsItem.ItemType column.
 *
 * Values verified against complete-database-11-02-2026.sql.
 * DB stores UPPERCASE varchar values.
 */
enum CmsItemType: string
{
    case Heading = 'HEADING';
    case Text = 'TEXT';
    case Html = 'HTML';
    case ButtonText = 'BUTTON_TEXT';
    case Link = 'LINK';
    case ImagePath = 'IMAGE_PATH';
    case Media = 'MEDIA';
    case Url = 'URL';

    /**
     * Attempts to create an enum from a nullable value.
     */
    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }

    /**
     * Checks if this item type contains text content.
     */
    public function isTextBased(): bool
    {
        return match ($this) {
            self::Heading, self::Text, self::ButtonText, self::Link, self::Url => true,
            self::Html, self::ImagePath, self::Media => false,
        };
    }

    /**
     * Checks if this item type contains HTML content.
     */
    public function isHtmlBased(): bool
    {
        return $this === self::Html;
    }

    /**
     * Checks if this item type references media assets.
     */
    public function isMediaBased(): bool
    {
        return match ($this) {
            self::Media, self::ImagePath => true,
            default => false,
        };
    }
}

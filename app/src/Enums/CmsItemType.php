<?php

declare(strict_types=1);

namespace App\Enums;

enum CmsItemType: string
{
    case Text = 'text';
    case Html = 'html';
    case Media = 'media';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}


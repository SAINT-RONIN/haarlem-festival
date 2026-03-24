<?php

declare(strict_types=1);

namespace App\Constants;

final class CmsMessages
{
    public const INVALID_CSRF = 'Invalid request token. Please refresh and try again.';
    public const NO_CHANGES = 'No changes submitted';
    public const UPDATE_FAILED = 'Failed to update content';
    public const UPDATE_UNEXPECTED_ERROR = 'An unexpected error occurred.';
    public const UPDATE_SUCCESS_TEMPLATE = 'Updated %d item(s) successfully';
    public const IMAGE_UPLOAD_SUCCESS = 'Image uploaded successfully';
    public const IMAGE_LINK_FAILED = 'Failed to link image';
    public const MISSING_ITEM_ID = 'Missing item ID';
    public const INVALID_PAGE_ID = 'Invalid page ID';
    public const NO_FILE_UPLOADED = 'No file uploaded';
    public const PAGE_NOT_FOUND = 'Page not found';
    public const IMAGE_LINK_SUCCESS = 'Image linked successfully';
    public const DEFAULT_ADMIN_NAME = 'Administrator';
}
